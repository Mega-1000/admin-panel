<?php namespace App\Services;

use App\Entities\Allegro_Auth;
use App\Entities\AllegroDispute;
use App\Entities\Order;
use App\Entities\SelTransaction;
use App\Jobs\AddLabelJob;
use App\Jobs\RemoveLabelJob;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\str;

class AllegroDisputeService
{

    const AUTH_RECORD_ID = 2;
    const STATUS_ONGOING = 'ONGOING';
    const STATUS_CLOSED = 'CLOSED';
    const TYPE_REGULAR = 'REGULAR';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Allegro_Auth
     */
    private $authModel;

    public function __construct()
    {
        $this->client = new Client();
        $this->authModel = Allegro_Auth::find(self::AUTH_RECORD_ID);
    }

    public function lookForNewDisputes(): void
    {
        $latest100disputes = $this->getDisputesList(0, 100); // 100 is vendor limit and should be fine here

        foreach ($latest100disputes as $dispute) {
            if (
                $dispute['status'] == 'ONGOING' &&
                AllegroDispute::where('form_id', '=', $dispute['checkoutForm']['id'])->count() === 0
            ) {
                $this->updateDisputeRecord($dispute['id']);
            }
        }
    }

    public function updateOngoingDisputes(): void
    {
        foreach (AllegroDispute::where('status', '=', self::STATUS_ONGOING)->get() as $dispute) {
            $this->updateDisputeRecord($dispute->dispute_id);
        }
    }

    public function updateDisputeRecord(string $disputeId): void
    {
        $disputeModel = AllegroDispute::firstOrNew(['dispute_id' => $disputeId]);
        $dispute = $this->getDispute($disputeId);

        if (!$disputeModel->id || ($disputeModel->hash && $disputeModel->hash !== $this->disputeHash($dispute))) {
            $disputeModel->unseen_changes = true;
        } else {
            $disputeModel->unseen_changes = false;
        }

        $disputeModel->hash = $this->disputeHash($dispute);
        $disputeModel->dispute_id = $dispute['id'];
        $disputeModel->status = $dispute['status'];
        $disputeModel->subject = $dispute['subject']['name'];
        $disputeModel->buyer_id = $dispute['buyer']['id'];
        $disputeModel->buyer_login = $dispute['buyer']['login'];
        $disputeModel->form_id = $dispute['checkoutForm']['id'];
        $disputeModel->ordered_date = $dispute['checkoutForm']['createdAt'];
        $disputeModel->order_id = $this->findOrderId($disputeModel);
        $disputeModel->save();

        $this->updateLabels($disputeModel);
    }

    public function getDisputesList(int $offset = 0, int $limit = 100)
    {
        $url = $this->getRestUrl("/sale/disputes?limit={$limit}&offset={$offset}");
        $response = $this->request('GET', $url, []);
        return json_decode((string)$response->getBody(), true)['disputes'];
    }

    public function getDispute(string $id): array
    {
        $url = $this->getRestUrl("/sale/disputes/{$id}");
        $response = $this->request('GET', $url, []);
        return json_decode((string)$response->getBody(), true);
    }

    public function getDisputeMessages(string $id): array
    {
        $result = [];
        $cursor = 0;

        do {
            $url = $this->getRestUrl("/sale/disputes/{$id}/messages?limit=100&offset={$cursor}");
            $response = $this->request('GET', $url, [
                'offset' => $cursor,
                'limit' => 100
            ]);
            $messages = json_decode((string)$response->getBody(), true)['messages'];
            $messagesCount = count($messages);
            $cursor += $messagesCount;
            $result = array_merge($result, $messages);
        } while ($messagesCount === 100);

        return $result;
    }

    public function sendMessage(string $disputeId, string $text, bool $endRequest = false)
    {
        $url = $this->getRestUrl("/sale/disputes/{$disputeId}/messages");
        $response = $this->request('POST', $url, [
            'text' => $text,
            'attachment' => null,
            'type' => self::TYPE_REGULAR
        ]);
        return json_decode((string)$response->getBody(), true);
    }

    public function getAuthCodes()
    {
        $response = $this->client->post(env('ALLEGRO_AUTH_CODES_URL'), [
            'headers' => [
                'Authorization' => $this->getBasicAuthString(),
                'Content-type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'client_id' => env('ALLEGRO_CLIENT_ID')
            ]
        ]);
        return json_decode((string)$response->getBody(), true);
    }

    public function checkAuthorizationStatus(string $deviceId)
    {
        $url = env('ALLEGRO_AUTH_STATUS_URL') . $deviceId;
        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => $this->getBasicAuthString(),
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function getAttachment(string $url)
    {
        $response = $this->request('GET', $url, []);
        $path = '/tmp/' . base64_encode($url);
        file_put_contents($path, (string)$response->getBody());
        return $path;
    }

    private function updateLabels(AllegroDispute $dispute)
    {
        if ($dispute->order_id && $dispute->status != self::STATUS_CLOSED) {
            if ($this->getDisputeMessages($dispute->dispute_id)[0]['author']['role'] != 'SELLER') {
                dispatch_now(new AddLabelJob($dispute->order->id, [186]));
                dispatch_now(new RemoveLabelJob($dispute->order->id, [185]));
            } else {
                dispatch_now(new AddLabelJob($dispute->order->id, [185]));
                dispatch_now(new RemoveLabelJob($dispute->order->id, [186]));
            }
        } else if ($dispute->status == self::STATUS_CLOSED) {
            dispatch_now(new RemoveLabelJob($dispute->order->id, [186]));
            dispatch_now(new RemoveLabelJob($dispute->order->id, [185]));
            dispatch_now(new AddLabelJob($dispute->order->id, [187]));
        }
    }

    private function refreshTokens()
    {
        $url = env('ALLEGRO_REFRESH_URL') . $this->getRefreshToken();
        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => $this->getBasicAuthString()
            ]
        ]);
        $response = json_decode((string)$response->getBody(), true);
        $this->authModel->access_token = $response['access_token'];
        $this->authModel->refresh_token = $response['refresh_token'];
        $this->authModel->save();
    }

    private function findOrderId(AllegroDispute $dispute): ?int
    {
        $transactionsIds = SelTransaction::where('tr_CheckoutFormId', '=', $dispute->form_id)->pluck('id');
        if (count($transactionsIds)) {
            $order = Order::whereIn('sello_id', $transactionsIds)->first();
        } else {
            $order = null;
        }
        if ($order) {
            return $order->id;
        } else {
            return null;
        }
    }

    private function request(string $method, string $url, array $params)
    {
        $headers = [
            #'Accept' => 'application/vnd.allegro.public.v1+json',
            'Authorization' => "Bearer " . $this->getAccessToken(),
            'Content-Type' => 'application/vnd.allegro.public.v1+json'
        ];

        try {
            $response = $this->client->request(
                $method,
                $url,
                [
                    'headers' => $headers,
                    'json' => $params
                ]
            );
        } catch (\Exception $e) {
            if ($e->getCode() == 401) {
                $this->refreshTokens();
                $response = $this->request($method, $url, $params);
            } else {
                return $this->cantGetDisputesAlert();
            }
        }
        return $response;
    }

    private function getAccessToken()
    {
        return $this->authModel->access_token;
    }

    private function getRefreshToken()
    {
        return $this->authModel->refresh_token;
    }

    private function getRestUrl(string $resource): string
    {
        return env('ALLEGRO_REST_URL') . $resource;
    }

    private function getBasicAuthString(): string
    {
        return 'Basic ' . base64_encode(env('ALLEGRO_CLIENT_ID') . ':' . env('ALLEGRO_CLIENT_SECRET'));
    }

    private function disputeHash(array $dispute): string
    {
        return md5(json_encode($dispute));
    }

    private function cantGetDisputesAlert(): bool
    {
        // what should we do in this case?
        return false;
    }

}

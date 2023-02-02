<?php namespace App\Services;

use App\Entities\Order;
use App\Jobs\AddLabelJob;
use App\Jobs\RemoveLabelJob;
use App\Entities\AllegroDispute;
use Illuminate\Database\Eloquent\Collection;

class AllegroDisputeService extends AllegroApiService
{
    protected $auth_record_id = 2;
    
    const STATUS_ONGOING = 'ONGOING';
    const STATUS_CLOSED = 'CLOSED';
    const TYPE_REGULAR = 'REGULAR';

    public function __construct()
    {
        parent::__construct();
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

    public function getNewPendingDisputes(): Collection {
        
        // get first not booked dispute
        $newDisputes = AllegroDispute::where('is_pending', 1)->where(function($query) {
            $user = auth()->user();
            $query->where('user_id', $user->id)->orWhereNull('user_id');
        })->get();

        if( $newDisputes->isEmpty() ) throw new \Exception('Nie znaleziono nowych dyskusji');

        return $newDisputes;
    }

    public function bookDispute(): AllegroDispute {
        $user = auth()->user();

        // check if user has any opened disputes
        $currentDispute = AllegroDispute::where([
            'user_id'    => $user->id,
            'is_pending' => 1,
        ])->first();

        if( $currentDispute !== null ) return $currentDispute;

        // get first not booked dispute
        $currentDispute = AllegroDispute::where([
            'is_pending' => 1,
            'user_id'    => null,
        ])->first();

        if( $currentDispute === null ) throw new \Exception('Nie znaleziono nowych dyskusji');

        $currentDispute->user_id = $user->id;
        $currentDispute->save();

        return $currentDispute;
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
        $order = Order::where('allegro_form_id', '=', $disputeModel->form_id)->first();
        
        if(!$order) return;

        $disputeModel->hash = $this->disputeHash($dispute);
        $disputeModel->dispute_id = $dispute['id'];
        $disputeModel->status = $dispute['status'];
        $disputeModel->subject = $dispute['subject']['name'];
        $disputeModel->buyer_id = $dispute['buyer']['id'];
        $disputeModel->buyer_login = $dispute['buyer']['login'];
        $disputeModel->form_id = $dispute['checkoutForm']['id'];
        $disputeModel->ordered_date = $dispute['checkoutForm']['createdAt'];
        $disputeModel->order_id = $order->id;

        $disputeModel->save();

        $this->updateLabels($disputeModel);
    }

    public function getDisputesList(int $offset = 0, int $limit = 100)
    {
        $url = $this->getRestUrl("/sale/disputes?limit={$limit}&offset={$offset}");
        if (!($response = $this->request('GET', $url, []))) {
        	return [];
        }
        
        return $response['disputes'];
    }

    public function getDispute(string $id): array
    {
        $url = $this->getRestUrl("/sale/disputes/{$id}");
	    if (!($response = $this->request('GET', $url, []))) {
		    return [];
	    }
        return $response;
    }

    public function getDisputeMessages(string $id): array
    {
        $result = [];
        $cursor = 0;

        do {
            $url = $this->getRestUrl("/sale/disputes/{$id}/messages?limit=100&offset={$cursor}");
            if (!($response = $this->request('GET', $url, [
                'offset' => $cursor,
                'limit' => 100
            ]))) {
            	break;
            }
            $messages = $response['messages'];
            $messagesCount = count($messages);
            $cursor += $messagesCount;
            $result = array_merge($result, $messages);
        } while ($messagesCount === 100);

        return $result;
    }

    public function sendMessage(string $disputeId, string $text, bool $endRequest = false, $attachment = null)
    {
        $url = $this->getRestUrl("/sale/disputes/{$disputeId}/messages");
        return $response = $this->request('POST', $url, [
            'text' => $text,
            'attachment' => $attachment,
            'type' => self::TYPE_REGULAR
        ]);
    }
    
    /** Create a request add attachment */
    public function createAttachmentId($fileName, $fileSize)
    {
        $url = $this->getRestUrl("/sale/dispute-attachments");
        if (!($response = $this->request('POST', $url, [
            'fileName' => $fileName,
            'size' => $fileSize
        ]))) {
        	return false;
        }

        return ($response && isset($response['id'])) ? $response['id'] : false;
    }
    /** Upload attachment */
    public function uploadAttachment($attachmentId, $contentsFile)
    {
        $url = $this->getRestUrl("/sale/dispute-attachments/" . $attachmentId);
        return $response = $this->request('PUT', $url, [], [
            'name' => 'file',
            'contents' => ($contentsFile),
            'filename' => $attachmentId
        ]);
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

    private function disputeHash(array $dispute): string
    {
        return md5(json_encode($dispute));
    }
}

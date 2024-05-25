<?php

namespace App\Jobs;

use App\DTO\Messages\CreateMessageDTO;
use App\Entities\Firm;
use App\Helpers\MessagesHelper;
use App\Services\MessageService;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClauteChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = $this->request['message'];
        $apiUrl = "https://api.anthropic.com/v1/messages";
        $apiKey = "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";
        $anthropicVersion = "2023-06-01";

        $data = [
            "model" => "claude-3-opus-20240229",
            "max_tokens" => 1024,
            "messages" => [
                ["role" => "user", "content" => '
                `You are part of my larvel system. You have to detect if user wants to add employee of company to the chat if so provide me json response like this`

`{ "AddCompany": "COMPANY NAME", "NoticeForUser": "change it to message for user", }`

`if user wants to add some compoany wich is not in list provide response like this`

`{ "NoticeForUser": "change it to message for user", }`

`There are only these companies: "IZOTERM" "POLSTYR" "SWISSPOR" "AAA"`

`There is also possibiliy to change date of spedition in this case you have to return response like this`

`{ "ChangeDates": "from: 25.05.2024 to: 30.05.2024", "NoticeForUser": "Zmieniłem daty klienta na: od 25.05.2024 do 30.05.2024", }`

`If user wants to perform onne of this actions to add otherwise return "No message" If you want to send message to user because user wants to perform one of actions but for example you need more info provide response replace notice for user with your message to get more into`

`{ "NoticeForUser": "change it to message for user", }`

All response are samples witch just represent format of response not actual response so change it accordingly.

Today is ‘ . now() . ’

`Do not provide any other type response it will break systemuser`

`prompt: "' . $message . '"`
                ']
            ]
        ];

        $payload = json_encode($data);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-key: $apiKey",
            "anthropic-version: $anthropicVersion",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            try {
                $response = json_decode(str_replace(',
}', '}', json_decode($response)->content[0]->text));
                if (isset($response->ChangeDates)) {
                    $dateRange = $response->ChangeDates;

                    if (preg_match('/from:\s*(\d{2}\.\d{2}\.\d{4})\s*to:\s*(\d{2}\.\d{2}\.\d{4})/', $dateRange, $matches)) {
                        $startDate = $matches[1];
                        $endDate = $matches[2];

                        $startDateTime = DateTime::createFromFormat('d.m.Y', $startDate);
                        $endDateTime = DateTime::createFromFormat('d.m.Y', $endDate);

                        $helper = new MessagesHelper($this->request['token']);
                        $order = $helper->getOrder();

                        $order->dates->update([
                            'customer_shipment_date_from' => $startDate,
                            'customer_shipment_date_to' => $endDate,
                            'customer_delivery_date_from' => $startDate,
                            'customer_delivery_date_to' => $endDate,
                        ]);
                    }
                }

                    if (isset($response->AddCompany)) {
                        $company = Firm::where('symbol', $response->AddCompany)->first();
                        $helper = new MessagesHelper($this->request['token']);
                        $order = $helper->getOrder();

                        foreach ($company->employees as $employee) {
                            $chatHelper = new MessagesHelper($order->chat->token);

                            $chatHelper->chatId = $order->chat->id;
                            $chatHelper->currentUserType = 'e';

                            $userId = MessageService::createNewCustomerOrEmployee($order->chat, new Request(['type' => 'Employee']), $employee);
                            $chatHelper->currentUserId = $userId;
                        }
                    }

                    if (isset($response->NoticeForUser)) {
                        $dto = CreateMessageDTO::fromRequest($this->request, $this->request['token']);
                        $dto->message = $response->NoticeForUser;

                        $helper = new MessagesHelper($this->request['token']);
                        $order = $helper->getOrder();

                        $message = app(MessagesHelper::class)->sendMessage(
                            $order->chat,
                            $dto->message,
                        );
                    }

            } catch (\Exception $exception) {
                dd($exception);
            }
        }

        curl_close($ch);
    }
}

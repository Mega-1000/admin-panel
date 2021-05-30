<?php

namespace App\Jobs;

use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerAddressRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class GetAllegroOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;



    const TOKEN_URI = 'https://allegro.pl/auth/oauth/token';

    const AUTHORIZATION_URI = 'https://allegro.pl/auth/oauth/device';

	const API_URL = 'https://api.allegro.pl';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderRepository $orderRepository, CustomerRepository $customerRepository, CustomerAddressRepository $customerAddressRepository)
    {
        $orders = $this->getOrders()["checkoutForms"];

        foreach($orders as $order) {
            if(!$orderRepository->findWhere(["allegro_transaction_id" => $order["id"]])->first()) {
                if(!$customer = $customerRepository->findWhere(["nick_allegro" => $order["buyer"]["login"]])->first()) {
                    $customer = $customerRepository->create([
                        'login' => $order["buyer"]["login"],
                        'password' => bcrypt("password"),
                        'nick_allegro' => $order["buyer"]["login"]
                    ]);
                   if(array_key_exists("address", $order["delivery"])) {
                        $customerAddress = $customerAddressRepository->create([
                            'customer_id' => $customer->id,
                            'type' => 'DELIVERY_ADDRESS',
                            'firstname' => $order["delivery"]["address"]["firstName"],
                            'lastname' => $order["delivery"]["address"]["lastName"],
                            'address' =>  $order["delivery"]["address"]["street"],
                            'postal_code' => $order["delivery"]["address"]["zipCode"],
                            'email' => $order["buyer"]["email"],
                        ]);
                   }
                    if(array_key_exists("address", $order["invoice"])) {
                        $customerAddress = $customerAddressRepository->create([
                            'customer_id' => $customer->id,
                            'type' => 'INVOICE_ADDRESS',
                            'firstname' => $order["invoice"]["address"]["firstName"],
                            'lastname' => $order["invoice"]["address"]["lastName"],
                            'address' =>  $order["invoice"]["address"]["street"],
                            'postal_code' => $order["invoice"]["address"]["zipCode"],
                            'email' => $order["buyer"]["email"],
                            'nip' => $order["invoice"]["address"]["company"]["taxId"],
                            'firmname' => $order["invoice"]["address"]["company"]["name"],
                        ]);
                    }
                }

                $order = $orderRepository->create([
                    'allegro_transaction_id' => $order["id"],
                    'total_price' => (float)$order["summary"]["totalToPay"]["amount"],
                    'shipment_price' => (isset($order["delivery"]["cost"]["amount"]) ? $order["delivery"]["cost"]["amount"] : 0.00) ,
                    'customer_id' => $customer->id,
                    'status_id' => 1,
                ]);
            }
        }
    }

    protected function apiAuth()
    {

		$curl = curl_init($this::AUTHORIZATION_URI);
		$header = [
			'Authorization: Basic ' . base64_encode(config('allegro.client_id') . ':' . config('allegro.client_secret')),
			'Content-Type: application/x-www-form-urlencoded'
		];

		$curl_post_data = array(
			'client_id' => config('allegro.client_id'),
		);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		$curl_response = curl_exec($curl);

		curl_close($curl);

		$decode = json_decode($curl_response, true);
		var_dump($decode);

    }

	protected function getToken($deviceCode)
	{
		$curl = curl_init($this::TOKEN_URI.'?grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Adevice_code&device_code=' . $deviceCode);
		$header = [
			'Authorization: Basic ' . base64_encode(config('allegro.client_id') . ':' . config('allegro.client_secret')),
		];

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		$curl_response = curl_exec($curl);

		curl_close($curl);

		$decode = json_decode($curl_response, true);
		return $decode;
	}

    protected function refreshToken(){
        $curl = curl_init($this::TOKEN_URI.'?grant_type=refresh_token&refresh_token=eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1NTQzMDExMTAsInVzZXJfbmFtZSI6IjM3ODU0MzM1IiwianRpIjoiYzJmOTdhYmYtZDE0OC00MzgyLThiOWMtNjNiMDdlY2M4NjQ5IiwiY2xpZW50X2lkIjoiOTkyNmQ3ZjJmZTU0NGYzM2IyMjdiYTc0NWUxYjZkYmQiLCJzY29wZSI6WyJhbGxlZ3JvX2FwaSJdLCJhdGkiOiJkYjk4ZTkyMC1kMDE0LTQ1MDEtOTljNC1jNGRiMWMyZWNjNDYifQ.iduF33W-JVTq0kmG8axS-0DLgCeasi1JSCPPTtY2Aips2lh7OuFBC_Py9SQy5quVWjGboslubDWArWrrtuzeEvj-u4Uh0usXmenBQrzmDAGC_p0BAvQlSEuRJ94Iz6VbLnAAm4VNpIQKmnSr1KgDSurDB3h_eQ30YSZORfAfu3yaunTsqROVsKt_hym6w3UnK1gW1s74NcF0HIprzvaaQ6jBYfqZlZiwcznSkmXmN8Cr08VPwDcMPTMkxOAojlID9ZWWcesHLzLB7ksgnDBw7V-tZVwIHuqiocJecnx6Twa25hEXpXVe-3LohQQqwtX8hugrzhgZIM6YjBgKPzj9cw&redirect_uri=http://localhost:8000');
        $header = [
            'Authorization: Basic ' . base64_encode(config('allegro.client_id') . ':' . config('allegro.client_secret')),
        ];

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        $curl_response = curl_exec($curl);

        curl_close($curl);

        $decode = json_decode($curl_response, true);

        return $decode;
    }

	protected function getOrders()
    {
        $curl = curl_init($this::API_URL.'/order/checkout-forms?limit=100');
        $header = [
            'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1NDY4MjMwNjcsInVzZXJfbmFtZSI6IjM3ODU0MzM1IiwianRpIjoiMGZlZDhiYWYtNjJjYS00MjgyLWI2MTUtMTg0Y2ExYzMwZTFhIiwiY2xpZW50X2lkIjoiOTkyNmQ3ZjJmZTU0NGYzM2IyMjdiYTc0NWUxYjZkYmQiLCJzY29wZSI6WyJhbGxlZ3JvX2FwaSJdfQ.3QFZ1QwX2XBMNaU62f-M3n2uBQH8V_zwY4VU0pwW_39b3YyBlosUfSExTIr8WiZxe2s3IPqbVwfLwdNA3vN19he7si_bEhO718uEvdMLp1rzzjjspsHerBxd0wH-aYlbnLXCN9hPGH0mcLcw7lftgNTaiYep3rh2OSYvvbUEVvIcCPyow12MimiEcemmnMcZXq9IpBT3yyqMZDCAX6FORklbRWOfMmgNqucNETld4RK9X6S2KaxgZfKAC6uspSjA27oV4wBiasg-0WJebxwr83YiqTe4P85S4EwyJnGdrtO3kHR7hxJsPVEgR7H_92UvAq5xwmXg3eGkiqXmBmZ-7w',
            'Accept: application/vnd.allegro.beta.v1+json'
        ];

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $curl_response = curl_exec($curl);

        curl_close($curl);

        $decode = json_decode($curl_response, true);
        return $decode;
    }
}

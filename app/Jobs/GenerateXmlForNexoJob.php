<?php

namespace App\Jobs;

use App\Integrations\Artoit\PreAdres;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TheSeer\Tokenizer\XMLSerializer;

class GenerateXmlForNexoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->orderRepository = app(OrderRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = $this->orderRepository->findWhereIn('id', [27344, 27343]);

        $xml = '';
        foreach ($orders as $order) {
            $customer = $order->customer;
            $customerStandardAddress = $customer->standardAddress();
            $preAddress = new PreAdres(
                null,
                $customerStandardAddress->address,
                $customerStandardAddress->city,
                $customerStandardAddress->postal_code,
                'Polska'
            );
//            $preAddre
            dom_import_simplexml($preAddress);
            \Opis\Closure\serialize()
            $xml = $this->generate_valid_xml_from_array([$preAddress]);
            dd($xml);
        }
    }

   public function generate_xml_from_array($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . "\n" . $this->generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
        }


        return $xml;
    }

    function generate_valid_xml_from_array($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

        $xml .= '<' . $node_block . '>' . "\n";
        $xml .= $this->generate_xml_from_array($array, $node_name);
        $xml .= '</' . $node_block . '>' . "\n";

        return $xml;
    }
}

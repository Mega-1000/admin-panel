<?php

namespace App\Console\Commands;

use App\Entities\ProductAnalyzer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AnalyzeProductPrices extends Command
{
	protected $signature = 'products:price-analyzer';
	
	protected $description = 'Price analyzer for products';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function handle()
	{
		$products = ProductAnalyzer::whereHas('product')->get();
		
		foreach ($products as $product) {
			$content = file_get_contents('https://allegro.pl/oferta/' . $product->parse_url);
			$price = null;

			if (preg_match('/"price":([0-9]{1,}(\.[0-9]{1,})?)/', $content, $matches)) {
				$price = $matches[1];
			}
			
			$product->analyze_date = Carbon::now();
			$product->price = $price;
			$product->save();
		}
	}
}

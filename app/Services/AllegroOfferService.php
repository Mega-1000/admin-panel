<?php namespace App\Services;

/**
 * Class AllegroOrderService
 * @package App\Services
 *
 */
class AllegroOfferService extends AllegroApiService
{
    protected $auth_record_id = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function listing($phrase, $category_id = false, $seller_id = false)
    {
        $params = [
        	'phrase' => $phrase
        ];
        
        if ($category_id) {
        	$params['category.id'] = $category_id;
        }
	
	    if ($seller_id) {
		    $params['seller.id'] = $seller_id;
        }
	    
        $url = $this->getRestUrl("/offers/listing?" . http_build_query($params));
        
        if (!($offers = $this->request('GET', $url, []))) {
        	return [];
        }
	    return $offers['items'];
    }
}

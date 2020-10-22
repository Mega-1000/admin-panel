<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class AllegroPackage extends Model
{
    const TABLE = 'allegro_package';
    const ID = 'id';
    const ALLEGRO_OPERATION_DATE = 'allegro_operation_date';
    const PACKAGE_SPEDITION_COMPANY_NAME = 'package_spedition_company_name';
    const PACKAGE_DELIVERY_COMPANY_NAME = 'package_delivery_company_name';
    const REAL_TOTAL_DELIVERY_COMPANY_COST = 'real_total_delivery_company_cost';
    const REAL_DELIVERY_COMPANY_COST = 'real_delivery_company_cost';
    const ALLEGRO_SUBSCRIPTION_COST = 'allegro_subscription_cost';
    const ADS_CAMPAIGN_FEE = 'ads_campaign_fee';
    const BILL_CORRECTION = 'bill_correction';
    const PREFERENCE_AUCTION_FEE = 'preference_auction_fee';
    const BOOKED_PAYMENT = 'booked_payment';
    const MONTH_SUMMARY = 'month_summary';
    const ALLEGRO_TRANSACTION_ID = 'allegro_transaction_id';
    const ALLEGRO_OFFER_NAME = 'allegro_offer_name';
    const RETURN_OF_COMMISSION_COST = 'return_of_commission_cost';
    const PACKAGE_ID = 'package_id';

    public $table = self::TABLE;

    public $fillable = [
        self::ALLEGRO_OPERATION_DATE,
        self::PACKAGE_SPEDITION_COMPANY_NAME,
        self::PACKAGE_DELIVERY_COMPANY_NAME,
        self::REAL_TOTAL_DELIVERY_COMPANY_COST,
        self::REAL_DELIVERY_COMPANY_COST,
        self::ALLEGRO_SUBSCRIPTION_COST,
        self::ADS_CAMPAIGN_FEE,
        self::BILL_CORRECTION,
        self::PREFERENCE_AUCTION_FEE,
        self::BOOKED_PAYMENT,
        self::MONTH_SUMMARY,
        self::ALLEGRO_TRANSACTION_ID,
        self::ALLEGRO_OFFER_NAME,
        self::RETURN_OF_COMMISSION_COST,
        self::PACKAGE_ID
    ];

    const RELATION_ORDER_PACKAGES = 'orderPackages';

    public function orderPackages() {
        return $this->belongsTo(OrderPackage::class,self::PACKAGE_ID);
    }

}

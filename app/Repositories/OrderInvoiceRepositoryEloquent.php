<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderInvoiceRepository;
use App\Entities\OrderInvoice;
use App\Validators\OrderInvoiceValidator;

/**
 * Class OrderInvoiceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderInvoiceRepositoryEloquent extends BaseRepository implements OrderInvoiceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderInvoice::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

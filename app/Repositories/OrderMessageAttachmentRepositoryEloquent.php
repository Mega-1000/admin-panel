<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderMessageAttachmentRepository;
use App\Entities\OrderMessageAttachment;
use App\Validators\OrderMessageAttachmentValidator;

/**
 * Class OrderMessageAttachmentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderMessageAttachmentRepositoryEloquent extends BaseRepository implements OrderMessageAttachmentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderMessageAttachment::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

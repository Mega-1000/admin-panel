<?php

namespace App\Repositories;

use App\Jobs\AutomaticMigration;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\LabelGroupRepository;
use App\Entities\LabelGroup;
use App\Validators\LabelGroupsValidator;

/**
 * Class LabelGroupsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LabelGroupRepositoryEloquent extends BaseRepository implements LabelGroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LabelGroup::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function create(array $attributes)
    {
        $edition = parent::create($attributes);
        AutomaticMigration::dispatch();
        return $edition;
    }

    public function update(array $attributes, $id)
    {
        $update = parent::update($attributes, $id);
        AutomaticMigration::dispatch();
        return $update;
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        $update = parent::updateOrCreate($attributes, $values);
        AutomaticMigration::dispatch();
        return $update;

    }

    public function delete($id)
    {
        $delete = parent::delete($id);
        AutomaticMigration::dispatch();
        return $delete;
    }

    public function deleteWhere(array $where)
    {
        $delete = parent::deleteWhere($where);
        AutomaticMigration::dispatch();
        return $delete;
    }
}

<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\PackageTemplate;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class PackageTemplateRepositoryEloquent extends BaseRepository implements PackageTemplateRepository
{
    public function model(): string
    {
        return PackageTemplate::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

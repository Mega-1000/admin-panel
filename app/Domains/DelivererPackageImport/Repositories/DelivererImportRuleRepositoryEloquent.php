<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Entities\Deliverer;
use App\Entities\DelivererImportRule;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class DelivererImportRuleRepositoryEloquent extends BaseRepository implements DelivererImportRuleRepositoryInterface
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function model(): string
    {
        return DelivererImportRule::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function saveImportRules(array $delivererImportRules): void
    {
        if (empty($delivererImportRules)) {
            return;
        }

        /* @var $rule DelivererImportRule */
        foreach($delivererImportRules as $rule) {
            $rule->save();
        }
    }

    public function removeAllDelivererImportRules(Deliverer $deliverer): int
    {
        return $this->deleteWhere(['deliverer_id' => $deliverer->id]);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Label;
use App\Entities\Task;
use App\Repositories\TaskRepository;

class TaskService
{
    /**
     * @var TaskRepository
     */
    protected TaskRepository $taskRepository;

    /**
     * TaskService constructor.
     *
     * @param TaskRepository $productRepository
     */
    public function __construct(TaskRepository $productRepository)
    {
        $this->taskRepository = $productRepository;
    }

    /**
     * @param array $courierArray
     * @return mixed
     */
    public function getTaskQuery(array $courierArray)
    {
        return $this->taskRepository->where('user_id', Task::WAREHOUSE_USER_ID)
            ->with(['taskTime' => function ($query) {
                $query->orderBy('date_start', 'asc');
            }])
            ->whereHas('order', function ($query) use ($courierArray) {
                $query->whereHas('packages', function ($query) use ($courierArray) {
                    $query->whereIn('service_courier_name', $courierArray);
                })->whereHas('labels', function ($query) {
                    $query
                        ->where('labels.id', Label::BLUE_HAMMER_ID);
                })->whereDoesntHave('labels', function ($query) {
                    $query->where('labels.id', Label::RED_HAMMER_ID)
                        ->orWhere('labels.id', Label::GRAY_HAMMER_ID)
                        ->orWhere('labels.id', Label::PRODUCTION_STOP_ID);
                });
            });
    }
}

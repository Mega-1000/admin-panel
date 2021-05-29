<?php

namespace App\Jobs;

use App\Entities\Task;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserWorkRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddNewWorkHourForUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        UserRepository $repository,
        UserWorkRepository $userWorkRepository,
        TaskRepository $taskRepository
    ) {
        $users = $repository->findWhere([['warehouse_id', '!=', null]]);
        if (count($users) > 0) {
            foreach ($users as $user) {
                $today = Carbon::today();
                for ($j = 0; $j < 3650; $j++) {
                    if ($user->userWorks->isEmpty()) {
                        $userWork = $userWorkRepository->create([
                            'user_id' => $user->id,
                            'date_of_work' => $j == 0 ? $today->toDateString() : $today->addDay()->toDateString(),
                            'start' => '08:00',
                            'end' => '16:00',
                        ]);
                        $dateStart = new Carbon($userWork->date_of_work . ' ' . $userWork->start);
                        $dateEnd = new Carbon($userWork->date_of_work . ' ' . $userWork->end);
                        $numberOfBreak = (strtotime($dateEnd->toDateTimeString()) - strtotime($dateStart->toDateTimeString())) / 3600 / 4;

                        if ($numberOfBreak >= 1) {
                            for ($i = 0; $i < $numberOfBreak; $i++) {
                                if ($i == 0) {
                                    $start = $dateStart->addHours(4)->toDateTimeString();
                                    $end = $dateStart->addMinutes(15)->toDateTimeString();
                                } elseif ($i == 1) {
                                    $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                                    $end = $dateStart->addMinutes(15)->toDateTimeString();
                                } elseif ($i == 2) {
                                    $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                                    $end = $dateStart->addMinutes(15)->toDateTimeString();
                                } elseif ($i == 3) {
                                    $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                                    $end = $dateStart->addMinutes(15)->toDateTimeString();
                                }

                                if (strtotime($dateEnd->toDateTimeString()) >= strtotime($start)) {
                                    $arrayTask = [
                                        'user_id' => $userWork->user->id,
                                        'warehouse_id' => $userWork->user->warehouse_id,
                                        'name' => 'Przerwa',
                                        'date_start' => $start,
                                        'date_end' => $end,
                                        'rendering' => 'background',
                                        'color' => Task::DISABLED_COLOR,
                                        'created_by' => 1,
                                        'status' => 'TO_DO'
                                    ];
                                    $arrayTaskClean = [
                                        'user_id' => $userWork->user->id,
                                        'warehouse_id' => $userWork->user->warehouse_id,
                                        'name' => 'Sprzątanie placu',
                                        'date_start' => $dateEnd->subMinutes(10)->toDateTimeString(),
                                        'date_end' => $dateEnd->addMinutes(10)->toDateTimeString(),
                                        'rendering' => 'background',
                                        'color' => Task::DISABLED_COLOR,
                                        'created_by' => 1,
                                        'status' => 'TO_DO'
                                    ];
                                    $checkTask = $taskRepository->with(['taskTime'])->whereHas('taskTime',
                                        function ($query) use ($dateStart) {
                                            $query->where([
                                                ['date_start', '>=', $dateStart->subHours(4)->toDateTimeString()],
                                                ['date_end', '<=', $dateStart->addHours(8)->addMinutes(15)]
                                            ]);
                                        })->findWhere([
                                        ['rendering', '=', 'background'],
                                        ['user_id', '=', $arrayTask['user_id']]
                                    ]);
                                    $checkTaskClean = $taskRepository->with(['taskTime'])->whereHas('taskTime',
                                        function ($query) use ($dateEnd) {
                                            $query->where([
                                                ['date_start', '>=', $dateEnd->subMinutes(10)->toDateTimeString()],
                                                ['date_end', '<=', $dateEnd->toDateTimeString()]
                                            ]);
                                        })->findWhere([
                                        ['rendering', '=', 'background'],
                                        ['user_id', '=', $arrayTaskClean['user_id']]
                                    ]);
                                    if (count($checkTask) == 0) {
                                        $task = $taskRepository->create($arrayTask);
                                        $task->taskTime()->create($arrayTask);
                                    } else {
                                        foreach ($checkTask as $task) {
                                            $task->delete();
                                        }
                                        $newTask = $taskRepository->create($arrayTask);
                                        $newTask->taskTime()->create($arrayTask);
                                    }
                                    if (count($checkTaskClean) == 0) {
                                        $task = $taskRepository->create($arrayTaskClean);
                                        $task->taskTime()->create($arrayTaskClean);
                                    } else {
                                        foreach ($checkTaskClean as $task) {
                                            $task->delete();
                                        }
                                        $newTask = $taskRepository->create($arrayTaskClean);
                                        $newTask->taskTime()->create($arrayTaskClean);
                                    }
                                }

                            }
                        }
                    } else {
                        $userWork = $userWorkRepository->findWhere([
                            ['user_id', '=', $user->id],
                            ['date_of_work', '=', $today->toDateString()]
                        ]);
                        if (count($userWork) == 0) {
                            $userWorkToday = $userWorkRepository->create([
                                'user_id' => $user->id,
                                'date_of_work' => $j == 0 ? $today->toDateString() : $today->toDateString(),
                                'start' => '08:00',
                                'end' => '16:00'
                            ]);

                            $dateStart = new Carbon($userWorkToday->date_of_work . ' ' . $userWorkToday->start);
                            $dateEnd = new Carbon($userWorkToday->date_of_work . ' ' . $userWorkToday->end);
                            $numberOfBreak = (strtotime($dateEnd->toDateTimeString()) - strtotime($dateStart->toDateTimeString())) / 3600 / 4;

                            if ($numberOfBreak >= 1) {
                                for ($i = 0; $i < $numberOfBreak; $i++) {
                                    if ($i == 0) {
                                        $start = $dateStart->addHours(4)->toDateTimeString();
                                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                                    } elseif ($i == 1) {
                                        $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                                    } elseif ($i == 2) {
                                        $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                                    } elseif ($i == 3) {
                                        $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                                    }

                                    if (strtotime($dateEnd->toDateTimeString()) >= strtotime($start)) {
                                        $arrayTask = [
                                            'user_id' => $userWorkToday->user->id,
                                            'warehouse_id' => $userWorkToday->user->warehouse_id,
                                            'name' => 'Przerwa',
                                            'date_start' => $start,
                                            'date_end' => $end,
                                            'rendering' => 'background',
                                            'color' => Task::DISABLED_COLOR,
                                            'created_by' => 1,
                                            'status' => 'TO_DO'
                                        ];

                                        $arrayTaskClean = [
                                            'user_id' => $userWorkToday->user->id,
                                            'warehouse_id' => $userWorkToday->user->warehouse_id,
                                            'name' => 'Sprzątanie placu',
                                            'date_start' => $dateEnd->subMinutes(10)->toDateTimeString(),
                                            'date_end' => $dateEnd->addMinutes(10)->toDateTimeString(),
                                            'rendering' => 'background',
                                            'color' => Task::DISABLED_COLOR,
                                            'created_by' => 1,
                                            'status' => 'TO_DO'
                                        ];

                                        $checkTask = $taskRepository->with(['taskTime'])->whereHas('taskTime',
                                            function ($query) use ($dateStart) {
                                                $query->where([
                                                    ['date_start', '>=', $dateStart->subHours(4)->toDateTimeString()],
                                                    ['date_end', '<=', $dateStart->addHours(8)->addMinutes(15)]
                                                ]);
                                            })->findWhere([
                                            ['rendering', '=', 'background'],
                                            ['user_id', '=', $arrayTask['user_id']]
                                        ]);
                                        $checkTaskClean = $taskRepository->with(['taskTime'])->whereHas('taskTime',
                                            function ($query) use ($dateEnd) {
                                                $query->where([
                                                    ['date_start', '>=', $dateEnd->subMinutes(10)->toDateTimeString()],
                                                    ['date_end', '<=', $dateEnd->toDateTimeString()]
                                                ]);
                                            })->findWhere([
                                            ['rendering', '=', 'background'],
                                            ['user_id', '=', $arrayTask['user_id']]
                                        ]);
                                        if (count($checkTask) == 0) {
                                            $task = $taskRepository->create($arrayTask);
                                            $task->taskTime()->create($arrayTask);
                                        } else {
                                            foreach ($checkTask as $task) {
                                                $task->delete();
                                            }
                                            $newTask = $taskRepository->create($arrayTask);
                                            $newTask->taskTime()->create($arrayTask);
                                        }
                                        if (count($checkTaskClean) == 0) {
                                            $task = $taskRepository->create($arrayTaskClean);
                                            $task->taskTime()->create($arrayTaskClean);
                                        } else {
                                            foreach ($checkTaskClean as $task) {
                                                $task->delete();
                                            }
                                            $newTask = $taskRepository->create($arrayTaskClean);
                                            $newTask->taskTime()->create($arrayTaskClean);
                                        }
                                    }
                                }
                            }
                        }
                        $today->addDay();
                    }
                }
            }
        }
    }
}

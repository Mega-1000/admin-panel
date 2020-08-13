<?php

namespace App\Http\Controllers;

use App\Entities\Task;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Repositories\UserWorkRepository;

/**
 * Class UserWorksController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserWorksController extends Controller
{
    /**
     * @var UserWorkRepository
     */
    protected $repository;

    protected $taskRepository;

    /**
     * UserWorksController constructor.
     *
     * @param UserWorkRepository $repository
     */
    public function __construct(UserWorkRepository $repository, TaskRepository $taskRepository)
    {
        $this->repository = $repository;
        $this->taskRepository = $taskRepository;
    }

    public function addWorkHours(Request $request)
    {
        $date = Carbon::today();
        $array = [];
        foreach($request->start as $key => $val){
            $array[$key]['start'] = $val;
            $array[$key]['user_id'] = $key;
            $array[$key]['date_of_work'] = $date->toDateString();
        }
        foreach($request->end as $key => $val){
            $array[$key]['end'] = $val;
        }
        foreach($array as $item){
            $userWork = $this->repository->findWhere([['user_id', '=', $item['user_id']], ['date_of_work', '=', $date->toDateString()]]);
            if($userWork->isEmpty()) {
                $userWork = $this->repository->create($item);
            } else {
                $userWork->first()->update($item);
            }
            $dateStart = new Carbon($userWork->first->id->date_of_work.' '.$userWork->first->id->start);
            $dateEnd = new Carbon($userWork->first->id->date_of_work.' '.$userWork->first->id->end);
            $numberOfBreak = (strtotime($dateEnd->toDateTimeString())-strtotime($dateStart->toDateTimeString()))/3600/4;

            if($numberOfBreak >= 1){
                for($i = 0; $i < (int)$numberOfBreak; $i++){
                    if($i == 0){
                        $start = $dateStart->addHours(4)->toDateTimeString();
                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                    } elseif($i == 1){
                        $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                    } elseif($i == 2){
                        $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                    } elseif($i == 3){
                        $start = $dateStart->subHours(4)->addHours(4)->toDateTimeString();
                        $end = $dateStart->addMinutes(15)->toDateTimeString();
                    }

                    if(strtotime($dateEnd->toDateTimeString()) >= strtotime($start)) {
                        $arrayTask = [
                            'user_id' => $userWork->first->id->user->id,
                            'warehouse_id' => $userWork->first->id->user->warehouse_id,
                            'name' => 'Przerwa',
                            'date_start' => $start,
                            'date_end' => $end,
                            'rendering' => 'background',
                            'color' => Task::DISABLED_COLOR,
                            'created_by' => 1,
                            'status' => 'TO_DO'
                        ];

                        $arrayTaskClean = [
                            'user_id' => $userWork->first->id->user->id,
                            'warehouse_id' => $userWork->first->id->user->warehouse_id,
                            'name' => 'Sprzątanie placu',
                            'date_start' => $dateEnd->subMinutes(10)->toDateTimeString(),
                            'date_end' => $dateEnd->addMinutes(10)->toDateTimeString(),
                            'rendering' => 'background',
                            'color' => Task::DISABLED_COLOR,
                            'created_by' => 1,
                            'status' => 'TO_DO'
                        ];
                        $checkTask = $this->taskRepository->with(['taskTime'])->whereHas('taskTime',
                            function ($query) use ($dateStart) {
                                $query->where([
                                    ['date_start', '>=', $dateStart->subHours(4)->toDateTimeString()],
                                    ['date_end', '<=', $dateStart->addHours(8)->addMinutes(15)]
                                ]);
                            })->findWhere([['rendering', '=', 'background'], ['user_id', '=', $arrayTask['user_id']]]);
                        $checkTaskClean = $this->taskRepository->with(['taskTime'])->whereHas('taskTime',
                            function ($query) use ($dateEnd) {
                                $query->where([
                                    ['date_start', '>=', $dateEnd->subMinutes(10)->toDateTimeString()],
                                    ['date_end', '<=', $dateEnd->toDateTimeString()]
                                ]);
                            })->findWhere([['rendering', '=', 'background'], ['user_id', '=', $arrayTask['user_id']]]);
                        if (count($checkTask) == 0) {
                            $task = $this->taskRepository->create($arrayTask);
                            $task->taskTime()->create($arrayTask);
                        } else {
                            foreach($checkTask as $task){
                                $task->delete();
                            }
                            $newTask = $this->taskRepository->create($arrayTask);
                            $newTask->taskTime()->create($arrayTask);
                        }
                        if (count($checkTaskClean) == 0) {
                            $task = $this->taskRepository->create($arrayTaskClean);
                            $task->taskTime()->create($arrayTaskClean);
                        } else {
                            foreach($checkTaskClean as $task){
                                $task->delete();
                            }
                            $newTask = $this->taskRepository->create($arrayTaskClean);
                            $newTask->taskTime()->create($arrayTaskClean);
                        }
                    }
                }
            }
        }

        return redirect()->back()->with([
            'message' => __('users.message.user_works_updated'),
            'alert-type' => 'success'
        ]);
    }

}

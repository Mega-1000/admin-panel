<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Labels\ScheduledTimeResetTypeCRequest;
use App\Http\Requests\Api\LabelsSetScheduledTimesRequest;
use App\Repositories\LabelGroupRepository;
use App\Repositories\LabelRepository;
use App\Repositories\OrderLabelSchedulerAwaitRepository;
use App\Repositories\OrderLabelSchedulerRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LabelsController extends Controller
{
    use ApiResponsesTrait;

    /** @var OrderLabelSchedulerAwaitRepository */
    protected $orderLabelSchedulerAwaitRepository;

    /** @var OrderLabelSchedulerRepository */
    protected $labelSchedulerRepository;

    /** @var LabelRepository */
    protected $labelRepository;

    /** @var LabelGroupRepository */
    protected $labelGroupRepository;

    /**
     * LabelsController constructor.
     * @param OrderLabelSchedulerAwaitRepository $orderLabelSchedulerAwaitRepository
     * @param OrderLabelSchedulerRepository $labelSchedulerRepository
     * @param LabelRepository $labelRepository
     * @param LabelGroupRepository $labelGroupRepository
     */
    public function __construct(
        OrderLabelSchedulerAwaitRepository $orderLabelSchedulerAwaitRepository,
        OrderLabelSchedulerRepository $labelSchedulerRepository,
        LabelRepository $labelRepository,
        LabelGroupRepository $labelGroupRepository
    ) {
        $this->orderLabelSchedulerAwaitRepository = $orderLabelSchedulerAwaitRepository;
        $this->labelSchedulerRepository = $labelSchedulerRepository;
        $this->labelRepository = $labelRepository;
        $this->labelGroupRepository = $labelGroupRepository;
    }

    public function getAssociatedLabelsToOrderFromGroup(Request $request, $labelGroupName)
    {
        $groupId = $this->labelGroupRepository->findWhere(['name' => $labelGroupName], ['id'])->first()['id'];

//        dd($groupId);
        //$labels = $this->labelRepository->findWhere();

        $labels = \DB::table('labels')
            ->distinct()
            ->select('labels.*')
            ->rightJoin('order_labels', 'order_labels.label_id', '=', 'labels.id')
//            ->leftJoin('labels', 'order_labels.label_id', '=', 'labels.id')
//            ->leftJoin('label_groups', 'label_groups.id', '=', 'labels.label_group_id')
            ->where(['labels.label_group_id' => $groupId])
        ->get();

        return $labels->toArray();

          dd($labels->toArray());
    }

    public function getLabelsSchedulerAwait(Request $request, $userId)
    {
        $result = $this->orderLabelSchedulerAwaitRepository->findWhere(['user_id' => $userId]);

        $arr = [];
        if (!count($result)) {
            return $arr;
        }

        foreach ($result as $item) {
            $arr[] = [
                'id' => $item->id,
                'labels_timed_after_addition_id' => $item->labels_timed_after_addition_id,
                'name' => $item->getMainLabelName()
            ];
        }

        return $arr;
    }

    public function setScheduledTimes(LabelsSetScheduledTimesRequest $request)
    {
        $data = $request->validated();
        foreach ($data['sendDates'] as $item) {
            $result = $this->orderLabelSchedulerAwaitRepository->find($item['id']);
            $config = $result->getSchedulerConfig();

            $this->addScheduler($result, $config, "to_add_type_c", $item);
            $this->addScheduler($result, $config, "to_remove_type_c", $item);

            $result->delete();
        }

        return $this->okResponse();
    }

    public function scheduledTimeResetTypeC(ScheduledTimeResetTypeCRequest $request)
    {
        $data = $request->validated();
        $this->labelSchedulerRepository->create([
            'order_id' => $data['order_id'],
            'label_id_to_handle' => $data['label_id_to_handle'],
            'type' => 'C',
            'action' => 'to_add_type_c',
            'trigger_time' => Carbon::make($data['trigger_time']),
        ]);
    }

    protected function addScheduler($awaits, $config, $action, $data)
    {
        if (!empty($config->$action)) {
            $this->labelSchedulerRepository->create([
                'order_id' => $awaits->order_id,
                'label_id' => $config->main_label_id,
                'label_id_to_handle' => $config->label_to_handle_id,
                'type' => 'C',
                'action' => $action,
                'trigger_time' => Carbon::make($data['date']),
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Label;
use App\Entities\Order;
use App\Http\Requests\LabelCreateRequest;
use App\Http\Requests\LabelUpdateRequest;
use App\Http\Requests\OrderEditLabel;
use App\Repositories\LabelGroupRepository;
use App\Repositories\LabelRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use JetBrains\PhpStorm\NoReturn;
use Log;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class LabelsController.
 *
 * @package namespace App\Http\Controllers;
 */
class LabelsController extends Controller
{
    /**
     * LabelsController constructor.
     * @param LabelRepository $repository
     * @param LabelGroupRepository $labelGroupRepository
     */
    public function __construct(
        readonly protected LabelRepository $repository,
        readonly protected LabelGroupRepository $labelGroupRepository
    )
    {
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $labelGroups = $this->labelGroupRepository->all();
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('labels'));
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        return view('labels.index', compact('visibilities', 'labelGroups'));
    }

    /**
     * @param LabelCreateRequest $request
     * @return RedirectResponse
     */
    public function store(LabelCreateRequest $request)
    {
        $label = $this->repository->create([
            'name' => $request->input('name'),
            'order' => $request->input('order'),
            'label_group_id' => $request->input('label_group_id'),
            'color' => '#' . $request->input('color'),
            'font_color' => '#' . $request->input('font_color'),
            'status' => $request->input('status'),
            'icon_name' => $request->input('icon_name'),
            'message' => $request->input('message'),
            'timed' => $request->has('isTimed'),
            'manual_label_selection_to_add_after_removal' => $request->input('manual_label_selection_to_add_after_removal') ?? 0,
        ]);

        $label->labelsToAddAfterAddition()->sync($request->input('labels_to_add_after_addition'));
        $label->labelsToAddAfterTimedLabel()->sync($request->input('labels_after_time'));
        $label->labelsToAddAfterRemoval()->sync($request->input('labels_to_add_after_removal'));
        $label->labelsToRemoveAfterAddition()->sync($request->input('labels_to_remove_after_addition'));
        $label->labelsToRemoveAfterRemoval()->sync($request->input('labels_to_remove_after_removal'));

        $timedLabels = $this->prepareTimedLabelsForDatabase(json_decode($request->input('timed_labels'), true));
        $label->timedLabelsAfterAddition()->sync($timedLabels);

        return redirect()->route('labels.edit', ['id' => $label->id])->with([
            'message' => __('labels.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        $labelGroups = $this->labelGroupRepository->all();
        $labels = $this->repository->all();

        return view('labels.create', compact('labelGroups', 'labels'));
    }

    protected function prepareTimedLabelsForDatabase($labels)
    {
        $arr = [];

        if (empty($labels)) {
            return $arr;
        }

        foreach ($labels as $id => $rows) {
            $arr[$id]['label_to_handle_id'] = $id;
            foreach ($rows as $name => $hours) {
                $arr[$id][$name] = str_replace(",", ".", $hours);
            }
        }

        return $arr;
    }

    public function detachLabelFromOrder(OrderEditLabel $request)
    {
        try {
            $request->validated();
            $order = Order::find($request->order_id);
            $order->labels()->detach($request->label_id);
            $user = Auth::user();
            $label = Label::find($request->label_id);
            $order->labels_log .= Order::formatMessage($user, "usunął etykietę: $label->name");
            $order->save();
            return response('success');
        } catch (Exception $exception) {
            Log::error('Nie udało się usunąć etykiety', ['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]);
            return response(['errors' => ['message' => "Niespodziewany błąd prosimy spróbować później"]], 400);
        }
    }

    /**
     * @param $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $label = $this->repository->find($id);
        $labels = $this->repository->all()->except($label->id);
        $labelGroups = $this->labelGroupRepository->all();
        $labelsToAddAfterAddition = $label->labelsToAddAfterAddition()->get();
        $labelsToAddAfterRemoval = $label->labelsToAddAfterRemoval()->get();
        $labelsToRemoveAfterAddition = $label->labelsToRemoveAfterAddition()->get();
        $labelsToRemoveAfterRemoval = $label->labelsToRemoveAfterRemoval()->get();
        $timedLabelsAfterAddition = $this->prepareTimedLabelsForFront($label->timedLabelsAfterAddition()->get());
        $labelsToAddAfterTime = $label->labelsToAddAfterTimedLabel;
        $timedLabel = $label->timed;

        $labelsToAddAfterAdditionIds = $this->getArrayOfIds($labelsToAddAfterAddition);
        $labelsToAddAfterRemovalIds = $this->getArrayOfIds($labelsToAddAfterRemoval);
        $labelsToRemoveAfterAdditionIds = $this->getArrayOfIds($labelsToRemoveAfterAddition);
        $labelsToRemoveAfterRemovalIds = $this->getArrayOfIds($labelsToRemoveAfterRemoval);
        $labelsToAddAfterTimeIds = $this->getArrayOfIds($labelsToAddAfterTime);

        return view('labels.edit', compact(
            'label',
            'labelGroups',
            'labels',
            'labelsToAddAfterAdditionIds',
            'labelsToAddAfterRemovalIds',
            'labelsToRemoveAfterAdditionIds',
            'labelsToRemoveAfterRemovalIds',
            'labelsToAddAfterTimeIds',
            'timedLabelsAfterAddition',
            'timedLabel'
        ));
    }

    protected function prepareTimedLabelsForFront($labels)
    {
        $arr = [];

        if (!count($labels)) {
            return $arr;
        }

        $types = ['to_add_type_a', 'to_remove_type_a', 'to_add_type_b', 'to_remove_type_b', 'to_add_type_c', 'to_remove_type_c'];

        foreach ($labels as $label) {
            $pivot = $label->pivot;

            foreach ($types as $type) {
                if (!empty($pivot[$type])) {
                    $arr[$pivot->label_to_handle_id][$type] = $pivot[$type];
                }
            }
        }

        return $arr;
    }

    protected function getArrayOfIds($collection)
    {
        $array = [];
        if (count($collection) > 0) {
            foreach ($collection as $item) {
                $array[] = $item->id;
            }
        }

        return $array;
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $label = Label::query()->findOrFail($id);

        $this->repository->delete($label->id);

        return redirect()->route('labels.index')->with([
            'message' => __('labels.message.delete'),
            'alert-type' => 'info'
        ]);
    }

    public function associatedLabelsToAddAfterRemoval($id)
    {
        $label = $this->repository->find($id);

        return $label->labelsToAddAfterRemoval()->get();
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function datatable()
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection()
    {
        $collection = $this->repository->with('labelGroup')->all();

        return $collection;
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    #[NoReturn] public function changeStatus($id): RedirectResponse
    {
        $productStocks = $this->repository->find($id);

        if (empty($productStocks)) {
            abort(404);
        }
        $dataToStore = [];
        $dataToStore['status'] = $productStocks['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        // TODO This can't work
        //$this->repository->update($dataToStore, $firm->id);

        return redirect()->back()->with([
            'message' => __('labels.message.change_status'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param LabelUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(LabelUpdateRequest $request, $id)
    {
        $label = $this->repository->find($id);

        if (empty($label)) {
            abort(404);
        }

        $this->repository->update([
            'name' => $request->input('name'),
            'order' => $request->input('order'),
            'label_group_id' => $request->input('label_group_id'),
            'color' => '#' . $request->input('color'),
            'font_color' => '#' . $request->input('font_color'),
            'status' => $request->input('status'),
            'icon_name' => $request->input('icon_name'),
            'message' => $request->input('message'),
            'timed' => $request->has('isTimed'),
            'manual_label_selection_to_add_after_removal' => $request->input('manual_label_selection_to_add_after_removal') ?? 0,
        ], $id);

        $label->labelsToAddAfterAddition()->sync($request->input('labels_to_add_after_addition'));
        $label->labelsToAddAfterTimedLabel()->sync($request->input('labels_after_time'));
        $label->labelsToAddAfterRemoval()->sync($request->input('labels_to_add_after_removal'));
        $label->labelsToRemoveAfterAddition()->sync($request->input('labels_to_remove_after_addition'));
        $label->labelsToRemoveAfterRemoval()->sync($request->input('labels_to_remove_after_removal'));

        $timedLabels = $this->prepareTimedLabelsForDatabase(json_decode($request->input('timed_labels'), true));
        $label->timedLabelsAfterAddition()->sync($timedLabels);

        return redirect()->route('labels.edit', ['id' => $label->id])->with([
            'message' => __('labels.message.update'),
            'alert-type' => 'success'
        ]);
    }
}

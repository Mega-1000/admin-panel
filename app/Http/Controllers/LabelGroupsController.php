<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Http\Requests\LabelGroupCreateRequest;
use App\Http\Requests\LabelGroupUpdateRequest;
use App\Repositories\LabelGroupRepository;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class LabelGroupsController
 * @package App\Http\Controllers
 */
class LabelGroupsController extends Controller
{
    /**
     * @var LabelGroupRepository
     */
    protected $repository;

    /**
     * LabelGroupsController constructor.
     * @param LabelGroupRepository $labelGroupRepository
     */
    public function __construct(LabelGroupRepository $labelGroupRepository)
    {
        $this->repository = $labelGroupRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('label_groups'));
        foreach($visibilities as $key => $row)
        {
            $visibilities[$key]->show = json_decode($row->show,true);
            $visibilities[$key]->hidden = json_decode($row->hidden,true);
        }

        return view('label_group.index',compact('visibilities'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('label_group.create');
    }


    /**
     * @param LabelGroupCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LabelGroupCreateRequest $request)
    {
        $labelGroup = $this->repository->create($request->all());

        return redirect()->route('label_groups.edit', ['id' => $labelGroup->id])->with([
            'message' => __('label_groups.message.store'),
            'alert-type' => 'success'
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $labelGroup = $this->repository->find($id);

        return view('label_group.edit', compact('labelGroup'));
    }

    /**
     * @param LabelGroupUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LabelGroupUpdateRequest $request, $id)
    {
        $labelGroup = $this->repository->find($id);
        if (empty($labelGroup)) {
            abort(404);
        }

        $this->repository->update($request->all(), $labelGroup->id);

        return redirect()->route('label_groups.edit', ['id' => $labelGroup->id])->with([
            'message' => __('label_groups.message.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $labelGroup = $this->repository->find($id);
        if (empty($labelGroup)) {
            abort(404);
        }

        $labelGroup->delete();

        return redirect()->route('label_groups.index')->with([
            'message' => __('label_groups.message.delete'),
            'alert-type' => 'info'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
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
        $collection = $this->repository->all();

        return $collection;
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Http\Requests\StatusCreateRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Repositories\LabelRepository;
use App\Repositories\StatusRepository;
use App\Repositories\TagRepository;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class StatusesController.
 *
 * @package namespace App\Http\Controllers;
 */
class StatusesController extends Controller
{
    /**
     * @var StatusRepository
     */
    protected $repository;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /** @var LabelRepository */
    protected $labelRepository;

    /**
     * StatusesController constructor.
     * @param StatusRepository $repository
     * @param TagRepository $tagRepository
     * @param LabelRepository $labelRepository
     */
    public function __construct(
        StatusRepository $repository,
        TagRepository $tagRepository,
        LabelRepository $labelRepository
    ) {
        $this->repository = $repository;
        $this->tagRepository = $tagRepository;
        $this->labelRepository = $labelRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('statuses'));
        foreach($visibilities as $key => $row)
        {
            $visibilities[$key]->show = json_decode($row->show,true);
            $visibilities[$key]->hidden = json_decode($row->hidden,true);
        }

        return view('statuses.index',compact('visibilities'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $tags = $this->tagRepository->all();
        $labels = $this->labelRepository->orderBy('name')->all();

        return view('statuses.create', compact('tags', 'labels'));
    }

    /**
     * @param StatusCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StatusCreateRequest $request)
    {
        $status = $this->repository->create([
            'name' => $request->input('name'),
            'color' => '#' . $request->input('color'),
            'status' => $request->input('status'),
            'message' => $request->input('message')
        ]);
        $status->labelsToAddOnChange()->sync($request->input('labels_to_add'));

        return redirect()->route('statuses.edit', ['id' => $status->id])->with([
            'message' => __('statuses.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $status = $this->repository->find($id);
        $tags = $this->tagRepository->all();
        $labels = $this->labelRepository->orderBy('name')->all();

        $collection = $status->labelsToAddOnChange()->get();
        $labelsToAddOnChange = [];
        if(count($collection) > 0) {
            foreach ($collection as $item) {
                $labelsToAddOnChange[] = $item->id;
            }
        }

        return view('statuses.edit', compact('status', 'tags', 'labels', 'labelsToAddOnChange'));
    }


    /**
     * @param StatusUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StatusUpdateRequest $request, $id)
    {
        $status = $this->repository->find($id);

        if (empty($status)) {
            abort(404);
        }

        $this->repository->update([
            'name' => $request->input('name'),
            'color' => '#' . $request->input('color'),
            'status' => $request->input('status'),
            'message' => $request->input('message')
        ], $id);

        $status->labelsToAddOnChange()->sync($request->input('labels_to_add'));

        return redirect()->route('statuses.edit', ['id' => $status->id])->with([
            'message' => __('statuses.message.update'),
            'alert-type' => 'success'
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $status = $this->repository->find($id);

        if (empty($status)) {
            abort(404);
        }

        $status->delete($status->id);

        return redirect()->route('statuses.index')->with([
            'message' => __('statuses.message.delete'),
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

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $firm = $this->repository->find($id);

        if (empty($firm)) {
            abort(404);
        }
        $dataToStore = [];
        $dataToStore['status'] = $firm['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->repository->update($dataToStore, $firm->id);

        return redirect()->back()->with([
            'message' => __('statuses.message.change_status'),
            'alert-type' => 'success'
        ]);
    }
}

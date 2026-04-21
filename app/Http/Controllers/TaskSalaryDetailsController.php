<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TaskSalaryDetailsCreateRequest;
use App\Http\Requests\TaskSalaryDetailsUpdateRequest;
use App\Repositories\TaskSalaryDetailsRepository;
use App\Validators\TaskSalaryDetailsValidator;

/**
 * Class TaskSalaryDetailsController.
 *
 * @package namespace App\Http\Controllers;
 */
class TaskSalaryDetailsController extends Controller
{
    /**
     * @var TaskSalaryDetailsRepository
     */
    protected $repository;

    /**
     * @var TaskSalaryDetailsValidator
     */
    protected $validator;

    /**
     * TaskSalaryDetailsController constructor.
     *
     * @param TaskSalaryDetailsRepository $repository
     * @param TaskSalaryDetailsValidator $validator
     */
    public function __construct(TaskSalaryDetailsRepository $repository, TaskSalaryDetailsValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $taskSalaryDetails = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $taskSalaryDetails,
            ]);
        }

        return view('taskSalaryDetails.index', compact('taskSalaryDetails'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TaskSalaryDetailsCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(TaskSalaryDetailsCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $taskSalaryDetail = $this->repository->create($request->all());

            $response = [
                'message' => 'TaskSalaryDetails created.',
                'data'    => $taskSalaryDetail->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $taskSalaryDetail = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $taskSalaryDetail,
            ]);
        }

        return view('taskSalaryDetails.show', compact('taskSalaryDetail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $taskSalaryDetail = $this->repository->find($id);

        return view('taskSalaryDetails.edit', compact('taskSalaryDetail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TaskSalaryDetailsUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(TaskSalaryDetailsUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $taskSalaryDetail = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'TaskSalaryDetails updated.',
                'data'    => $taskSalaryDetail->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'TaskSalaryDetails deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'TaskSalaryDetails deleted.');
    }
}

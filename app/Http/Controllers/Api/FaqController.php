<?php

namespace App\Http\Controllers\Api;

use App\Repositories\FaqRepository;
use Illuminate\Http\Request;

/**
 * Klasa kontrolera obsługująca
 * @package App\Http\Controllers\Api
 *
 * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
 */
class FaqController
{
    use ApiResponsesTrait;

    /**
     * @var FaqRepository
     */
    protected $repository;

    /**
     * FaqController constructor.
     * @param FaqRepository $faqRepository
     */
    public function __construct(FaqRepository $faqRepository)
    {
        $this->repository = $faqRepository;
    }

    /**
     * Akcja do zapisywania pytań
     *
     * @param Request $request
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function store(Request $request)
    {
        $response = [];
        try {
            $result = $this->repository->create(
                [
                    'category' => $request->get('category'),
                    'questions' => $request->get('questions'),
                ]
            );
            $response['status'] = 200;
        } catch (\Exception $exception) {
            $response = [
                'status' => 500,
                'error' => $exception->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function getQuestions()
    {
        $result = [];
        $rawQuestions = $this->repository->all(['category', 'questions']);
        foreach ($rawQuestions as $value) {
            $result[$value->category] = array_merge($result[$value->category] ?? [], $value->questions);
        }

        return response()->json($result, 200);
    }

    public function index()
    {
        return response()->json($this->repository->all(), 200);
    }

    public function show($id)
    {
        return response()->json($this->repository->find($id), 200);
    }

    public function update($id, Request $request)
    {
        $response = [];
        try {
            $result = $this->repository->update([
                'questions' => $request->get('questions'),
            ], $id);
            if ($result) {
                $response['status'] = 200;
            }
            $this->repository->find($id)->refresh();
        } catch (\Exception $exception) {
            $response = [
                'status' => 500,
                'error' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        $response = [];
        try {
            $result = $this->repository->delete($id);
            if ($result) {
                $response['status'] = 200;
            }
        } catch (\Exception $exception) {
            $response = [
                'status' => 500,
                'error' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }
}
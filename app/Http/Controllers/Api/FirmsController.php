<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Firms\FirmUpdateRequest;
use App\Repositories\FirmRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class FirmsController
{
    use ApiResponsesTrait;

    protected $repository;

    public function __construct(FirmRepository $repository)
    {
        $this->repository = $repository;
    }

    public function updateData(FirmUpdateRequest $request, $id)
    {
        try {
            $request->validated();
            $firm = $this->repository->find($id);

            if ($firm->send_request_to_update_data) {
                $dataToStore = $request->all();

                if (empty($firm)) {
                    abort(404);
                }

                $dataToStore['send_request_to_update_data'] = false;
                $firm->update($dataToStore['general']);
                $firm->address->update($dataToStore['address']);
                $firm->warehouses->first->id->address->update($dataToStore['warehouse']['address']);
                $firm->warehouses->first->id->property->update($dataToStore['warehouse']['property']);
                $firm->employees->first->id->update($dataToStore['employee']);


                return $this->createdResponse();
            } else {
                return $this->notFoundResponse();
            }
        } catch (Exception $e) {
            Log::error('Problem with update firms data.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

}

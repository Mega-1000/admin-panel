<?php

namespace App\Http\Controllers\Api;

use App\Entities\TrackerLogs;
use App\Entities\WorkingEvents;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\WorkingEventRepository;
use App\User;
use Faker\Provider\DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkingEventsController extends Controller
{
    use ApiResponsesTrait;

    /** @var WorkingEventRepository */
    protected $workingEventRepository;

    /** @var UserRepository */
    protected $userRepository;

    /**
     * WorkingEvent constructor.
     * @param WorkingEventRepository $workingEventRepository
     */
    public function __construct(
        WorkingEventRepository $workingEventRepository,
        UserRepository         $userRepository
    )
    {
        $this->workingEventRepository = $workingEventRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Zwraca klientów z transakcjami
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function index(Request $request): JsonResponse
    {
        $response = $criteria = [];

        try {
            if ($request->has('userId')) {
                $criteria['user_id'] = $request->get('userId');
            }
            if ($request->has('date') && $request->get('date') !== 'null') {
                $date = new \DateTime($request->get('date'));
                $criteria[] = ['created_at', 'like', $date->format('Y-m-d') . '%'];
            } else {
                $criteria[] = ['created_at', 'like', (new \DateTime())->format('Y-m-d') . '%'];
            }
            $result = $this->workingEventRepository->findWhere($criteria);

            if (!empty($result)) {
                $response['status'] = 200;
                $response['workingEvents'] = [];

                foreach ($result as $item) {
                    $response['workingEvents'][] = [
                        'title' => $item->getTitle(),
                        'content' => $item->getContent(),
                        'date' => $item->created_at,
                        'orderId' => $item->order_id,
                        'userId' => $item->user_id,
                    ];
                }
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak transakcji'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }

        return response()->json($response);
    }

    /**
     * Zwraca klientów z transakcjami
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function inactivity(Request $request): JsonResponse
    {
        $response = [];

        try {
            $query = TrackerLogs::query();

            if ($request->has('userId')) {
                $query->where('user_id', '=', $request->get('userId'));
            }
            if ($request->has('date')) {
                $date = new \DateTime($request->get('date'));
                $query->where('created_at', 'like', $date->format('Y-m-d') . '%');
            }
            $result = $query->get();
            if (!empty($result)) {
                $response['status'] = 200;
                $response['inactivity'] = [];
                foreach ($result as $item) {
                    $response['inactivity'][] = [
                        'title' => $item->getTitle(),
                        'content' => $item->getContent(),
                        'date' => $item->created_at,
                        'userId' => $item->user_id,
                    ];
                }
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak transakcji'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }

    public function workers(): JsonResponse
    {
        $response = [];
        try {
            $result = $this->userRepository->findWhere([]);

            if (!empty($result)) {
                $response['status'] = 200;
                foreach ($result as $item) {
                    $response['users'][] = [
                        'id' => $item->id,
                        'firstname' => $item->firstname,
                        'lastname' => $item->lastname,
                    ];
                }
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak użytkowników'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }
}

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

            if ($result->isNotEmpty()) {
                $response['workingEvents'] = [];
                $response['status'] = 200;
                $interval = $result->first()->created_at->diff($result->last()->created_at);
                $response['workInfo'] = [
                    'workingFrom' => $result->first()->created_at->format('Y-m-d H:i:s'),
                    'workingTo' => $result->last()->created_at->format('Y-m-d H:i:s'),
                    'uptimeInMinutes' => $interval->h * 60 + $interval->i
                ];
                foreach ($result as $item) {
                    $response['workingEvents'][] = [
                        'title' => $item->getTitle(),
                        'content' => $item->getContent(),
                        'date' => $item->created_at->format('Y-m-d H:i:s'),
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
            if ($result->isNotEmpty()) {
                $response['status'] = 200;
                $response['inactivity'] = [];
                $response['workInfo']['idleTimeSummaryInMinutes'] = 0;
                foreach ($result as $item) {
                    $response['workInfo']['idleTimeSummaryInMinutes'] += $item->time;
                    $response['inactivity'][] = [
                        'id' => $item->id,
                        'title' => $item->getTitle(),
                        'content' => $item->getContent(),
                        'description' => $item->description,
                        'time' => $item->time,
                        'page' => $item->page,
                        'date' => $item->created_at->format('Y-m-d H:i:s'),
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

    /**
     * Usunięcie bezczynności
     *
     * @param TrackerLogs $trackerLogs Transakcja
     *
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function destroy(TrackerLogs $trackerLogs): JsonResponse
    {
        $response = [];
        try {
            $result = TrackerLogs::delete($trackerLogs->id);
            if ($result) {
                $response['status'] = 200;
            }
        } catch (\Exception $exception) {
            $response = [
                'status' => 424,
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response, $response['status']);
    }
}

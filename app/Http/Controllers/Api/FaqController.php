<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessagesHelper;
use App\Mail\AskQuestion;
use App\Repositories\FaqRepository;
use App\Repositories\OrderAllegroRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Entities\FaqCategoryIndex;
use Illuminate\Support\Arr;
use App\Entities\Faq;
use App\Http\Requests\SetPositionRequest;

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
    protected $faqRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderAllegroRepository
     */
    protected $orderAllegroRepository;

    /**
     * FaqController constructor.
     *
     * @param FaqRepository          $faqRepository
     * @param OrderRepository        $orderRepository
     * @param OrderAllegroRepository $orderAllegroRepository
     */
    public function __construct(
        FaqRepository $faqRepository,
        OrderRepository $orderRepository,
        OrderAllegroRepository $orderAllegroRepository)
    {
        $this->faqRepository = $faqRepository;
        $this->orderRepository = $orderRepository;
        $this->orderAllegroRepository = $orderAllegroRepository;
    }

    /**
     * Akcja do zapisywania pytań
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function store(Request $request)
    {
        $response = [];
        try {
            $result = $this->faqRepository->create(
                [
                    'category' => $request->get('category'),
                    'questions' => $request->get('questions'),
                ]
            );
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

    /**
     * Pobranie zestawów pytań i odpowiedzi.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function getQuestions()
    {
        $result = [];
        $rawQuestions = $this->faqRepository->all(['id', 'category', 'questions']);
        foreach ($rawQuestions as $value) {
            $result[$value->category] = array_merge($result[$value->category] ?? [], array_map(function ($item) use ($value) {
                $item['id'] = $value->id;
                $item['questions'] = $value->questions;
                return $item;
            }, $value->questions));
        }

        foreach ($result as &$value) {
            foreach ($value as &$item) {
                $index = FaqCategoryIndex::where('faq_id', $item['id']);
                $item['index'] = $index->exists() ? $index->first()->faq_category_index : null; 
            }
        }

        return response()->json($result, 200);
    }

    /**
     * Pobranie listy pytań i odpowiedzi do tabeli
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function index()
    {
        return response()->json($this->faqRepository->all(), 200);
    }


    /**
     * Pobranie pojedynczego rekordu.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function show($id)
    {
        return response()->json($this->faqRepository->find($id), 200);
    }


    /**
     * Aktualizacja rekordu.
     *
     * @param integer $id Identyfikator ścieżki.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function update($id, Request $request)
    {
        $response = [];
        try {
            $result = $this->faqRepository->update([
                'questions' => $request->get('questions'),
            ], $id);
            if ($result) {
                $response['status'] = 200;
            }
            $this->faqRepository->find($id)->refresh();
        } catch (\Exception $exception) {
            $response = [
                'status' => 500,
                'error' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }


    /**
     * Usunięcie ścieżki.
     *
     * @param integer $id Identyfikator ścieżki
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function destroy($id)
    {
        $response = [];
        try {
            $faq = $this->faqRepository->find($id);
            FaqCategoryIndex::where('faq_id', $id)->delete();
            FaqCategoryIndex::where('faq_category_name', $faq->category)->delete();
            $result = $faq->delete();
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


    public function askQuestion(Request $request)
    {
        $response = [];
        try {
            if ($request->filled('orderId')) {
                $order = $this->orderRepository->find($request->get('orderId'));
            } elseif ($request->filled('allegroOrderId')) {
//                $order = $this->orderAllegroRepository->findByField('order_id', $request->get('orderId'))->order;
            } else {
                $result = \Mailer::create()
                    ->to('info@mega1000.pl')
                    ->send(new AskQuestion(
                        $request->get('firstName'),
                        $request->get('lastName'),
                        $request->get('details'),
                        $request->get('phone'),
                        $request->get('email')
                    ));
            }

            if (!empty($order)) {
                $helper = new MessagesHelper();
                $helper->orderId = $order->id;
                $helper->currentUserId = $order->customer_id;
                $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
                $helper->createNewChat();
                $helper->addMessage($request->get('details'));
                $result = true;
            }
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

    public function getCategories()
    {
        $result = [];
        $categories = DB::table('faqs')
            ->select('category')
            ->distinct()
            ->get();
        foreach ($categories as $category) {
            $result[] = $category->category;
        }

        $faqCategoryIndex = FaqCategoryIndex::where('faq_category_type', 'category')->get();
        $faqCategoryIndex = $faqCategoryIndex->sortBy('faq_category_index');
        $faqCategoryIndex = $faqCategoryIndex->pluck('faq_category_name')->toArray();
        $result = array_merge($faqCategoryIndex, array_diff($result, $faqCategoryIndex));

        return response()->json($result);
    }

    public function setCategoryPosition(SetPositionRequest $request)
    {
        FaqCategoryIndex::where('faq_category_type', 'category')->delete();

        foreach ($request->validated('categories') as $key => $category) {
            $faqCategoryIndex = new FaqCategoryIndex();
            $faqCategoryIndex->faq_category_name = $category;
            $faqCategoryIndex->faq_category_index = $key;
            $faqCategoryIndex->save();
        }

        return response()->json(['status' => 200]);
    }

    public function setQuestionsPosition(SetPositionRequest $request) {
        foreach ($request->validated('categories') as $key => $question) {
        
            $faq = Faq::find($question['id']);

            FaqCategoryIndex::where('faq_category_type', 'question')
                ->where('faq_id', $question['id'])
                ->delete();


            $index = new FaqCategoryIndex();
            $index->faq_category_type = 'question';
            $index->faq_id = $question['id'];
            $index->faq_category_index = $key;
            $index->save();
        }
    }
}
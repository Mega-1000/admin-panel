<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Models\Setting;
use App\Services\AllegroChatService;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AllegroCommissionParser;
use App\Http\Requests\AllegroSetCommission;

class AllegroController extends Controller
{
    protected $allegroChatService;

    public function __construct(AllegroChatService $allegroChatService) {
        $this->allegroChatService = $allegroChatService;
    }

    public function setCommission(AllegroSetCommission $request)
    {
        $this->validate($request, $request->rules());

        try {
            $file = $request->file('file');
            $path = Storage::put('user-files/', $file);
            $path = Storage::path($path);

            if (($handle = fopen($path, "r")) === FALSE) {
                throw new \Exception('Nie można otworzyć pliku');
            }
            $parser = new AllegroCommissionParser();
            $parsedData = $parser->parseFile($handle);
            $errors = $parsedData['errors'];
            $newLetters = $parsedData['new_letters'];
            $newOrders = $parsedData['new_orders'];
            fclose($handle);
            Storage::delete($path);
            $route = redirect()->route('orders.index');
            if ($errors) {
                $route->with(
                    'allegro_commission_errors', $errors
                );
            }
            if ($newLetters) {
                $route->with('allegro_new_letters', $newLetters);
            }
            if ($newOrders) {
                $route->with('allegro_new_orders_from_comission', $newOrders);
            }
            return $route->with(['message' => __('voyager.generic.successfully_updated'),
                'alert-type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with(['message' => __('voyager.generic.update_failed'),
                'alert-type' => 'error']);
        }
    }

    public function createNewLetter(Request $request)
    {
        $data = $request->all();
        $letters = json_decode($data['letters']);
        foreach ($letters as $letter) {
            $nr = $letter->letter_number;
            $amount = $letter->real_cost_for_company;
            $courierName = $letter->courier_name;
            $parser = new AllegroCommissionParser();
            $pack = AllegroCommissionParser::CreatePack($nr, $amount, $courierName);
            $parser->createNewPackage($pack, $letter->form_id);
        }
        return redirect()->route('orders.index')->with(['message' => __('voyager.generic.successfully_updated'),
            'alert-type' => 'success']);
    }

    public function createNewOrder(Request $request)
    {
        $data = $request->all();
        $orders = json_decode($data['ids']);
        $parser = new AllegroCommissionParser();
        foreach ($orders as $id) {
            $parser->createNewOrder($id);
        }
        return redirect()->route('orders.index')->with(['message' => __('voyager.generic.successfully_updated'),
            'alert-type' => 'success']);
    }

    public function editTerms()
    {
        return view('allegro.edit-terms');
    }

    public function saveTerms(Request $request)
    {
        $setting = Setting::where('key','=','allegro.new_allegro_order_msg')->first();
        $setting->value = $request->get('content');
        $setting->save();
        return redirect()->route('orders.index')->with(['message' => __('voyager.generic.successfully_updated'),
            'alert-type' => 'success']);
    }

    // CHAT
    // https://developer.allegro.pl/documentation#tag/Message-Center
    
    // get chat threads
    public function listThreads() {
        $res = $this->allegroChatService->listThreads();

        return response()->json($res);
    }
    // get messages from specified thread
    public function listMessages(string $threadId) {
        $res = $this->allegroChatService->listMessages($threadId);

        return response()->json($res);
    }
    // get messages from specified thread
    public function downloadAttachment(string $attachmentId) {
        $res = $this->allegroChatService->downloadAttachment($attachmentId);

        return response()->json($res);
    }
    // post new message
    public function newMessage(array $data = []) {

        $data = [
            'recipient' => [
                'login' => 'sebex142',
            ],
            'text' => 'Testowa odpowiedź',
        ];

        $res = $this->allegroChatService->newMessage($data);

        return response()->json($res);
    }
    // post attachment declaration (preparation before upload)
    public function newAttachmentDeclaration(array $data = []) {

        $res = $this->allegroChatService->newAttachmentDeclaration($data);

        return response()->json($res);
    }
    // put read flag on thread
    public function changeReadFlagOnThread(string $threadId) {

        $data = [
            'read' => true
        ];

        $res = $this->allegroChatService->changeReadFlagOnThread($threadId, $data);

        return response()->json($res);
    }
    // mark particular thread as read
    public function uploadAttachment(string $attachmentId, string $contentsFile) {

        $res = $this->allegroChatService->uploadAttachment($attachmentId, $contentsFile);

        return response()->json($res);
    }
}

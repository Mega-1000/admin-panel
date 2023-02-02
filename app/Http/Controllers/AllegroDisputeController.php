<?php namespace App\Http\Controllers;

use App\Entities\AllegroDispute;
use App\Services\AllegroDisputeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AllegroDisputeController extends Controller
{

    /**
     * @var AllegroDisputeService
     */
    private $allegroDisputeService;

    public function __construct(AllegroDisputeService $service)
    {
        $this->allegroDisputeService = $service;
    }

    public function list()
    {
        $disputes = AllegroDispute::orderBy('unseen_changes', 'desc')->paginate(50);
        return view('disputes.list', [
            'disputes' => $disputes
        ]);
    }

    public function view($id)
    {
        $dispute = AllegroDispute::find($id);
        $dispute->unseen_changes = false;
        $dispute->save();
        $messages = $this->allegroDisputeService->getDisputeMessages($dispute->dispute_id);
        $this->allegroDisputeService->updateDisputeRecord($dispute->dispute_id);

        return view('disputes.view', [
            'dispute' => $dispute,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $dispute = AllegroDispute::find($id);
        $this->allegroDisputeService->sendMessage($dispute->dispute_id, $request->get('text'), false);
        return redirect('/admin/disputes/view/' . $dispute->id);
    }

    public function getAttachment($url)
    {
        $url = base64_decode($url);
        $path = $this->allegroDisputeService->getAttachment($url);
        return response()->download($path);
    }

    public function getNewPendingDisputes(): JsonResponse {
        try {
            $pendingDisputes = $this->allegroDisputeService->getNewPendingDisputes();
        } catch (\Exception $ex) {
            return response()->json( ['error' => $ex->getMessage()] );
        }

        return response()->json($pendingDisputes);
    }

    public function bookDispute(): JsonResponse {
        try {
            $bookedDispute = $this->allegroDisputeService->bookDispute();
        } catch (\Exception $ex) {
            return response()->json( ['error' => $ex->getMessage()] );
        }

        return response()->json($bookedDispute);
    }

    public function exitDispute(): JsonResponse {
        try {
            $isSuccess = $this->allegroDisputeService->exitDispute();
        } catch (\Exception $ex) {
            return response()->json( ['error' => $ex->getMessage()] );
        }

        return response()->json(['isSuccess' => $isSuccess]);
    }

}

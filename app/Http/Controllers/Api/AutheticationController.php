<?php


namespace App\Http\Controllers\Api;


use App\Entities\Auth_code;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutheticationController extends Controller
{
    const TOKEN_ERROR = 'token timed out';

    public function getToken(Request $request, $id)
    {
        try {
            if (!$this->isCorrectRequestFromBrowser($request)) {
                return response('success', 200);
            }
            $token = Auth_code::findOrFail($id);
            if ($token->created_at < Carbon::now()->subDays(1)) {
                throw new Exception(self::TOKEN_ERROR);
            }
            $user = $token->customer;
            $token->delete();
            $timestamp = Carbon::now()->addDay()->timestamp;
            return response(['access_token' => $user->createToken('Api code')->accessToken,
                'expires_in' => CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR * CarbonInterface::SECONDS_PER_MINUTE], 200);
        } catch (Exception $e) {
            if ($e->getMessage() != self::TOKEN_ERROR) {
                Log::error('Error: authenticate user by code', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
            return response('Something went wrong', 403);
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isCorrectRequestFromBrowser(Request $request): bool
    {
        return $request->headers->has('referer');
    }
}

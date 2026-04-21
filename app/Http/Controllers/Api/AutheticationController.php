<?php


namespace App\Http\Controllers\Api;


use App\Entities\Auth_code;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AutheticationController extends Controller
{
    const TOKEN_ERROR = 'token timed out';

    public function getToken(Request $request, $id): JsonResponse
    {
        try {
            if (!$this->isCorrectRequestFromBrowser($request)) {
                return response()->json('success');
            }

            $token = Auth_code::where('token', $id)->first();

            if ($token->created_at < Carbon::now()->subDays()) {
                throw new Exception(self::TOKEN_ERROR);
            }

            $user = $token->customer;
            $token->delete();

            return response()->json(['access_token' => $user->createToken('Api code')->accessToken,
                'expires_in' => CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR * CarbonInterface::SECONDS_PER_MINUTE], 200);
        } catch (Exception $e) {
            if ($e->getMessage() != self::TOKEN_ERROR) {
                Log::error('Error: authenticate user by code', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }

            return response()->json('Something went wrong', 403);
        }
    }

    public function loginByCode(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_code' => 'required|string|max:128|alpha_num',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Nieprawidłowy lub wygasły kod.'], 401);
        }

        try {
            $authCode = Auth_code::where('token', $request->user_code)
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->first();

            if (!$authCode) {
                Log::warning('Failed login attempt via user_code', ['ip' => $request->ip()]);
                return response()->json(['message' => 'Nieprawidłowy lub wygasły kod.'], 401);
            }

            $customer = $authCode->customer;
            $authCode->delete();

            $tokenResult = $customer->createToken('cart-restore');

            return response()->json([
                'token_type'   => 'Bearer',
                'expires_in'   => CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR * CarbonInterface::SECONDS_PER_MINUTE,
                'access_token' => $tokenResult->accessToken,
            ]);
        } catch (Exception $e) {
            Log::error('Error in loginByCode', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Nieprawidłowy lub wygasły kod.'], 401);
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

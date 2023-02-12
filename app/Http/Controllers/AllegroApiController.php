<?php

namespace App\Http\Controllers;

use App\Entities\Allegro_Auth;
use App\Services\AllegroApiService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AllegroApiController extends Controller
{
    public function auth_device($device_code = false): null|Application|View|Factory
    {
        $allegroService = new AllegroApiService();
        if ($device_code) {
            if (!($response = $allegroService->checkAuthorizationStatus($device_code))) {
                response('something wrong');
            }

            $authModel = Allegro_Auth::findOrNew(2);
            $authModel->access_token = $response['access_token'];
            $authModel->refresh_token = $response['refresh_token'];
            $authModel->save();
            return null;
        }
        if (!($res = $allegroService->getAuthCodes())) {
            response('something wrong');
        }

        return view('allegro.api.auth-device', $res);
    }

    public function auth_oauth2(Request $request): Application|View|Factory|null
    {
        $allegroService = new AllegroApiService();
        if (!$request->has('code')) {
            $query = [
                'response_type' => 'code',
                'client_id' => config('allegro.client_id'),
                'redirect_uri' => url()->current(),
            ];

            $url = $allegroService->getAuthUrl('/authorize') . '?' . http_build_query($query);

            return view('allegro.api.auth2', compact('url'));
        }

        if (!($response = $allegroService->authToken($request->code))) {
            response('fetch token error: token: ' . $request->code);
        }

        $authModel = Allegro_Auth::findOrNew(3);
        $authModel->access_token = $response['access_token'];
        $authModel->refresh_token = $response['refresh_token'];
        $authModel->save();
        return null;
    }
}

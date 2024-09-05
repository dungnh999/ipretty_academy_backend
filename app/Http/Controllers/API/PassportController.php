<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\RegisterAPIRequest;
use App\Http\Requests\API\AuthenticationAPIRequest;
use App\Repositories\UserRepository;
use App\Models\SilcoinCampaign;

class PassportController extends AppBaseController
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->userRepository = $userRepository;
    }

    public function register(RegisterAPIRequest $request)
    {
        $inputs = $request->all();
        if (isset($inputs[URI_PARAM_REGISTER_REFERRAL_CAMPAIGN])) {
            $silcoin_campaign = SilcoinCampaign::where('service_type_id', $inputs[URI_PARAM_REGISTER_REFERRAL_CAMPAIGN])->first();
            if ($silcoin_campaign) {
                $inputs['referral_campaign'] = $silcoin_campaign->id;
            }
        }
        $this->userRepository->registerAccountUser($inputs);
        return $this->sendSuccess(__('auth.registration.success_message'));
    }

    public function login(AuthenticationAPIRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::attempt($credentials)) {
            if (empty(Auth::user()->email_verified_at)) {
                return $this->sendError(
                    __('auth.unverify_email'),
                    403
                );
            }
            $token = Auth::user()->createToken(env('APP_NAME', 'Silcoin Platform'))->accessToken;
            return $this->sendResponse([
                'access_token' => $token,
                'current_user' => Auth::user()->toArray()], 
                __('auth.login.success_message')
            );
        } else {
            return $this->sendError(
                __('auth.failed'),
                401
            );
        }
    }

    public function getMe()
    {
        if (Auth::check()) {
            $me = Auth::user();
            return response()->json([
                'success' => true,
                'user' => $me
            ]);
        } else {
            return $this->sendError(
                'Unauthorized'
            );
        }
    }
}

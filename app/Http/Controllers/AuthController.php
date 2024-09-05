<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Response;


class AuthController extends AppBaseController
{
    public function index()
    {
      return view('contents.auth.login');
    }


    public function login(Request $request){
        $input['email'] = $request->get('email');
        $input['password'] = $request->get('password');
        if(!auth()->attempt($input)) {
          auth()->logout();
          return $this->sendError(
              __('messages.login_errors.username_or_password_incorrect'), 200
            );
        }else {
          $user = auth()->user();
          if($user->menuroles != ENUM_POSITION_STUDENT){
            if ($user->email_verified_at) {
              $user->latest_active_at = date(Carbon::now()->addMinutes(2));
              $user->save();
              $response = UserResource::make($user)->resolve();
              Session::put('account_info', $response);
              return Response::json([
                'message' => __('auth.login.success_message'),
                'data' => $response,
                'status' => 200
              ], 200);
            }else{
              auth()->logout();
              return $this->sendError(
                __('messages.login_errors.account_has_not_yet_verified'), 200
              );
            }
          }else{
            auth()->logout();
            return $this->sendError(
              __('messages.login_errors.account_has_not_manager'), 200
            );
          }
        }
    }

    public function logout(Request $request){
      auth()->logout();
      return Response::json([
        'message' => __('auth.login.logout_successfully'),
        'data' => '',
        'status' => 200
      ], 200);
    }
}

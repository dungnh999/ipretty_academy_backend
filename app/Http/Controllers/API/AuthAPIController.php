<?php

namespace App\Http\Controllers\API;

use App\Contract\CommonBusiness;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\AuthenticationAPIRequest;
use App\Http\Requests\API\RegisterAPIRequest;
use App\Http\Requests\API\ResetPasswordAPIRequest;
use App\Http\Requests\API\UpdatePasswordInSecurityAPIRequest;
use App\Http\Resources\UserResource;
use App\Jobs\PushNotificationWhenActiveAccount;
use App\Jobs\PushNotificationWhenNewAccount;
use App\Models\ResetPassword;
use App\Models\User;
use App\Notifications\ResetPasswordSuccess;
use App\Repositories\AuthenticationRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;


class AuthAPIController extends AppBaseController
{

    use CommonBusiness;
    /** @var  SilcoinCampaignRepository */
    private $authenticationRepository;

    public function __construct(AuthenticationRepository $authenticationRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->authenticationRepository = $authenticationRepo;
    }

    public function authentication (AuthenticationAPIRequest $request)
    {
      $validated = $request->validated();
      $input["email"] = $request->email;
        $input["password"] = $request->password;
        $response = $this->authenticationRepository->login($input);
        if($response != null) {
            if (isset($response["active"]) && $response["active"] == false) {
                return $this->sendError(
                    __('messages.login_errors.account_has_not_yet_verified'),
                    400
                );
            }

            if (isset($response["isLocked"]) && $response["isLocked"]) {
                return $this->sendError(
                    __('auth.login.account_locked'),
                  400
                );
            }

            $user = $response["user"];
            $user->latest_active_at = date(Carbon::now()->addMinutes(2));
            $user->save();
            if (isset($request->isAdminPage) &&
            $request->isAdminPage == true &&
            !$user->hasRole('admin') && (!$user->hasPermissionTo(PERMISSION["MANAGE_COURSES"]) && !$user->hasPermissionTo(PERMISSION["MANAGE_STUDENTS"]))) {
                return $this->sendError(
                    __('messages.errors.not_permission'),
                    403
                );
            }

            if (isset($request->isAdminPage)) {
                $response["isAdminPage"] = $request->isAdminPage;
            }

            $response["user"] = new UserResource($response["user"]);
            return $this->sendSuccess(__('auth.login.success_message'), $response);
        }else {
            return $this->sendError(
                __('messages.login_errors.username_or_password_incorrect'), 400
            );
        }
    }

    public function register (Request $request) {

        $input = $request->all();
        $input['position'] = ENUM_PREFIX_ROLE_EMPLOYEE;
        $user = $this->authenticationRepository->register($input);
        if(isset($user['is-exist'])){
            return $this->sendError(
                __('messages.login_errors.username_exist'),
                400
            );
        }
        $job = (new PushNotificationWhenNewAccount($user));
        dispatch($job);
        $this->pushNotificationForUser('admin');
        return $this->sendSuccess(__('auth.registration.success_message'), $user);
    }

    public function changePassword(UpdatePasswordInSecurityAPIRequest $request)
    {
        if (!Auth::check()) {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }

        $input = $request->all();

        $response = $this->authenticationRepository->changePassword($input);

        if (!$response) {

            $errors = [
                "current_password" => __('auth.reset_password.old_password_doesnt_matched')
            ];

            return $this->sendErrorForValidation(__('auth.reset_password.old_password_doesnt_matched'), 422, $errors);
        }else {

            return $this->sendSuccess( __('auth.reset_password.password_update_success'));
        }
    }

    public function activate(Request $request)
    {
        $id =  $request->get('id');
        $token = $request->get('token');

        $redirect = env('IPRETTY_PLATFORM') . '/#/';
        $active_url = env('ACTIVE_URL') ? env('ACTIVE_URL') : 'confirm-success';
        try {
            $user = User::find($id);
            if (!$user) {
                return redirect($redirect . '?error=' . __('messages.user_not_exist'))->with(
                    'error',
                    __('messages.user_not_exist')
                );
            }
            // check if token is expired

//            if (
//                Carbon::parse($user->updated_at)
//                    ->addDays(1)
//                    ->isPast()
//            ) {
//                return redirect($redirect . '?error=' . __('messages.expired_token'))->with(
//                    'error',
//                    __('messages.expired_token')
//                );
//            }
//
//            if ($user->activation_token != $token && $user->email_verified_at == null) {
//                return redirect($redirect . '?error=' . __('messages.invalid_token'))->with(
//                    'error',
//                    __('messages.invalid_token')
//                );
//            }
            if ($user->markEmailAsVerified()) {
                $job = new PushNotificationWhenActiveAccount($user);
                dispatch($job);
//                return redirect(
//                    $redirect . $active_url . '?email=' . $user->email . '&token=' . $user->activation_token . '&logout=1'
//                )->with('verifySuccess', __('messages.verification_successfully'));
                return $this->sendSuccess('Xác nhận thành công');
            }

        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => [
                        '_messages' => ['There is error while activating your account. ' . $e->getMessage()],
                    ],
                ],
                500
            );
        }
        return response()->json(
            [
                'errors' => [
                    '_messages' => ['There is unknown error while activating your account.'],
                ],
            ],
            500
        );
    }

    public function resetPassword(ResetPasswordAPIRequest $request)
    {

        $passwordReset = ResetPassword::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset) {
            return $this->sendError(__('messages.errors.notFound_or_isUsed'), 404);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return $this->sendError(__('messages.errors.user_notFound'), 404);
        }

        $user->password = bcrypt($request->password);

        $user->save();

        $passwordReset->delete();

        $user->notify(new ResetPasswordSuccess($passwordReset));

        return $this->sendSuccess(__('auth.reset_password.password_update_success'));
    }

    public function loginByToken (Request $request)
    {

        $input = $request->all();

        $response = $this->authenticationRepository->loginByToken($input);


        if ($response != null) {

            if (isset($response["active"]) && $response["active"] == false) {
                return $this->sendError(
                    __('messages.login_errors.account_has_not_yet_verified'),
                    401
                );
            }

            if (isset($response["isLocked"]) && $response["isLocked"]) {
                return $this->sendError(
                    __('auth.login.account_locked'),
                    401
                );
            }

            if (isset($response["emailOrToken"]) && $response["emailOrToken"] == false) {
                return $this->sendError(
                    __('auth.login.session_expired'),
                    401
                );
            }

            $user = $response["user"];
            $user->latest_active_at = date(Carbon::now()->addMinutes(2));
            $user->save();

            if (isset($request->isAdminPage) && $request->isAdminPage == true && !$user->hasRole('admin') && !$user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
                return $this->sendError(
                    __('messages.errors.not_permission'),
                    403
                );
            }

            if (isset($request->isAdminPage)) {
                $response["isAdminPage"] = $request->isAdminPage;
            }

            $response["user"] = new UserResource($response["user"]);
            // $user_id = $response["user"]['id'];

            return Response::json([
                'message' => __('auth.login.success_message'),
                'data' => $response
            ], 200);
        } else {

            return $this->sendError(
                __('auth.login.session_expired'),
                401
            );
        }
    }

}

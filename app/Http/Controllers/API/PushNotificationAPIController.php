<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePushNotificationAPIRequest;
use App\Http\Requests\API\UpdatePushNotificationAPIRequest;
use App\Repositories\PushNotificationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\PushNotificationResource;
use App\Jobs\PushWhenPublishedNewPushNotification;
use App\Notifications\PushNotification;
use App\Repositories\UserRepository;
use Response;

/**
 * Class PushNotificationController
 * @package App\Http\Controllers\API
 */

class PushNotificationAPIController extends AppBaseController
{
    /** @var  PushNotificationRepository */
    private $pushNotificationRepository;
    private $userRepository;

    public function __construct(PushNotificationRepository $pushNotificationRepo, UserRepository $userRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->pushNotificationRepository = $pushNotificationRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the PushNotification.
     * GET|HEAD /pushNotifications
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $params = request()->query();

        $pushNotifications = $this->pushNotificationRepository->allNotifications($params);

        return $this->sendResponse(
            $pushNotifications,
            __('messages.retrieved', ['model' => __('models/pushNotifications.plural')])
        );
    }

    /**
     * Store a newly created PushNotification in storage.
     * POST /pushNotifications
     *
     * @param CreatePushNotificationAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePushNotificationAPIRequest $request)
    {
        $input = $request->all();

        $user = auth()->user();

        if (isset($request->notification_cat) && $request->notification_cat != null) {
            
            $cat = $request->notification_cat;
            switch ($cat) {
                case 'AD':
                    $input['group_receivers'] = 'user,employee';
                    break;
                case 'DOC':
                case 'POL':
                case 'HOL':
                case 'FUNC':
                    $input['group_receivers'] = 'teacher,user,employee';
                    break;
                default:
                    # code...
                    break;
            }
        }

        $input['created_by'] = $user->id;

        $pushNotification = $this->pushNotificationRepository->create($input);

        if ( $pushNotification && $pushNotification->isPublished) {

            $receivers = isset($input['group_receivers']) ? $input['group_receivers'] : null;

            $type = isset($input['notification_cat']) ? $input['notification_cat'] : null;

            $job = (new PushWhenPublishedNewPushNotification($input['notification_message'], $receivers, $type));
            dispatch($job);
        }

        $message = __('messages.saved', ['model' => __('models/pushNotifications.singular')]);

        if (isset($input['isPublished']) && $input['isPublished'] == 1) {
            $message = __('messages.published_notification');

        }
        return $this->sendResponse(
            new PushNotificationResource($pushNotification),
            $message
        );
    }

    /**
     * Display the specified PushNotification.
     * GET|HEAD /pushNotifications/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PushNotification $pushNotification */
        $pushNotification = $this->pushNotificationRepository->find($id);

        if (empty($pushNotification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/pushNotifications.singular')])
            );
        }

        return $this->sendResponse(
            new PushNotificationResource($pushNotification),
            __('messages.retrieved', ['model' => __('models/pushNotifications.singular')])
        );
    }

    /**
     * Update the specified PushNotification in storage.
     * PUT/PATCH /pushNotifications/{id}
     *
     * @param int $id
     * @param UpdatePushNotificationAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePushNotificationAPIRequest $request)
    {
        $input = $request->all();

        /** @var PushNotification $pushNotification */
        $old_pushNotification = $this->pushNotificationRepository->find($id);

        if (empty($old_pushNotification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/pushNotifications.singular')])
            );
        }

        if ($old_pushNotification->isPublished) {
            return $this->sendError(
                __('messages.cannot_update_published_notification')
            );
        }

        if (isset($request->notification_cat) && $request->notification_cat != null) {

            $cat = $request->notification_cat;
            switch ($cat) {
                case 'AD':
                    $input['group_receivers'] = 'user,employee';
                    break;
                case 'DOC':
                case 'POL':
                case 'HOL':
                case 'FUNC':
                    $input['group_receivers'] = 'teacher,user,employee';
                    break;
                default:
                    # code...
                    break;
            }
        }

        $pushNotification = $this->pushNotificationRepository->update($input, $id);
        if ($pushNotification && $old_pushNotification->isPublished != 1 && $input['isPublished']) {

            $receivers = isset($input['group_receivers']) ? $input['group_receivers'] : null;

            $job = (new PushWhenPublishedNewPushNotification($input['notification_message'], $receivers));
            dispatch($job);
        }

        $message = __('messages.updated', ['model' => __('models/pushNotifications.singular')]);

        if (isset($input['isPublished']) && $input['isPublished'] == 1) {
            $message = __('messages.published_notification');
        }

        return $this->sendResponse(
            new PushNotificationResource($pushNotification), $message
        );
    }

    /**
     * Remove the specified PushNotification from storage.
     * DELETE /pushNotifications/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PushNotification $pushNotification */
        $pushNotification = $this->pushNotificationRepository->find($id);

        if (empty($pushNotification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/pushNotifications.singular')])
            );
        }

        $pushNotification->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/pushNotifications.singular')])
        );
    }
}

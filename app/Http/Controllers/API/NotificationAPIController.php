<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationAPIRequest;
use App\Http\Requests\API\UpdateNotificationAPIRequest;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\NotificationResource;
use Response;

/**
 * Class NotificationController
 * @package App\Http\Controllers\API
 */

class NotificationAPIController extends AppBaseController
{
    /** @var  NotificationRepository */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Notification.
     * GET|HEAD /notifications
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $notifications = $this->notificationRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );
        $params = request()->query();

        $notifications = $this->notificationRepository->getAll($params);
     
        return $this->sendResponse(
            $notifications,
            __('messages.retrieved', ['model' => __('models/notifications.plural')])
        );
        
    }

    /**
     * Store a newly created Notification in storage.
     * POST /notifications
     *
     * @param CreateNotificationAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateNotificationAPIRequest $request)
    {
        $input = $request->all();

        $notification = $this->notificationRepository->create($input);

        return $this->sendResponse(
            new NotificationResource($notification),
            __('messages.saved', ['model' => __('models/notifications.singular')])
        );
    }

    /**
     * Display the specified Notification.
     * GET|HEAD /notifications/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        return $this->sendResponse(
            new NotificationResource($notification),
            __('messages.retrieved', ['model' => __('models/notifications.singular')])
        );
    }

    /**
     * Update the specified Notification in storage.
     * PUT/PATCH /notifications/{id}
     *
     * @param int $id
     * @param UpdateNotificationAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotificationAPIRequest $request)
    {
        $input = $request->all();

        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        $notification = $this->notificationRepository->update($input, $id);

        return $this->sendResponse(
            new NotificationResource($notification),
            __('messages.updated', ['model' => __('models/notifications.singular')])
        );
    }

    /**
     * Remove the specified Notification from storage.
     * DELETE /notifications/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        $notification->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/notifications.singular')])
        );
    }

    public function setCheckStatusTheNotifications()
    {
        $notifications = $this->notificationRepository->setCheckStatusTheNotifications();
        return $this->sendResponse(
            $notifications,
            __('messages.retrieved', ['model' => __('models/notifications.plural')])
        );

        return $this->sendResponse(
            true,
            __('messages.retrieved', ['model' => __('models/notifications.plural')])
        );
    }

    public function readAllNotifications(Request $request)
    {
        $input = $request->all();
        if(isset($input['id'])) 
        {
            $notification = $this->notificationRepository->find($input['id']);
            if (empty($notification)) {
                return $this->sendError(
                    __('messages.not_found', ['model' => __('models/notifications.singular')])
                );
            }
        }else {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        $this->notificationRepository->readAllNotifications($input);
        return $this->sendResponse(
            true,
            __('messages.retrieved', ['model' => __('models/notifications.plural')])
        );
    }
}

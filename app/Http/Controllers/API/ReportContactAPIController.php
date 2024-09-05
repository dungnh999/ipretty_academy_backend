<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateReportContactAPIRequest;
use App\Models\ReportContact;
use App\Repositories\ReportContactRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateReportErrorAPIRequest;
use App\Http\Requests\API\CreateSendContactAPIRequest;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Resources\ReportContactResource;
use App\Jobs\PushNotificationWhenSubmitRegisterReceiveInformation;
use App\Models\User;
use App\Notifications\sendContact;
use Illuminate\Support\Facades\Notification;
use Response;

/**
 * Class ReportContactController
 * @package App\Http\Controllers\API
 */

class ReportContactAPIController extends AppBaseController
{
    /** @var  ReportContactRepository */
    private $reportContactRepository;

    public function __construct(ReportContactRepository $reportContactRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });

        $this->reportContactRepository = $reportContactRepo;
    }

    /**
     * Display a listing of the ReportContact.
     * GET|HEAD /reportContacts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $reportContacts = $this->reportContactRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            ReportContactResource::collection($reportContacts),
            __('messages.retrieved', ['model' => __('models/reportContacts.plural')])
        );
    }

    /**
     * Store a newly created ReportContact in storage.
     * POST /reportContacts
     *
     * @param CreateReportContactAPIRequest $request
     *
     * @return Response
     */
    public function sendReportError(CreateReportErrorAPIRequest $request)
    {
        $input = $request->all();

        $input['isReport'] = true;

        $reportContact = $this->reportContactRepository->createReport($input, $request);

        return $this->sendResponse(
            new ReportContactResource($reportContact),
            __('messages.sended_report_error')
        );
    }

    public function sendContact(CreateSendContactAPIRequest $request)
    {
        $input = $request->all();

        $input['isReport'] = false;

        $reportContact = $this->reportContactRepository->createReport($input, $request);
        
        $message = __('messages.sended_contact');

        $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->get();

        if(count($admins) > 0 ){

            foreach($admins as $admin) {
                
                if($admin)
                { 
                    
                    Notification::send($admin, new sendContact($reportContact));
                }
            }
        }

        return $this->sendResponse(
            new ReportContactResource($reportContact),
            $message
        );
    }

    /**
     * Display the specified ReportContact.
     * GET|HEAD /reportContacts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ReportContact $reportContact */
        $reportContact = $this->reportContactRepository->find($id);

        if (empty($reportContact)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/reportContacts.singular')])
            );
        }

        return $this->sendResponse(
            new ReportContactResource($reportContact),
            __('messages.retrieved', ['model' => __('models/reportContacts.singular')])
        );
    }

    /**
     * Update the specified ReportContact in storage.
     * PUT/PATCH /reportContacts/{id}
     *
     * @param int $id
     * @param UpdateReportContactAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportContactAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportContact $reportContact */
        $reportContact = $this->reportContactRepository->find($id);

        if (empty($reportContact)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/reportContacts.singular')])
            );
        }

        $reportContact = $this->reportContactRepository->update($input, $id);

        return $this->sendResponse(
            new ReportContactResource($reportContact),
            __('messages.updated', ['model' => __('models/reportContacts.singular')])
        );
    }

    /**
     * Remove the specified ReportContact from storage.
     * DELETE /reportContacts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ReportContact $reportContact */
        $reportContact = $this->reportContactRepository->find($id);

        if (empty($reportContact)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/reportContacts.singular')])
            );
        }

        $reportContact->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/reportContacts.singular')])
        );
    }

    public function receiveInformation(StoreEmailRequest $request) {

        $job = (new PushNotificationWhenSubmitRegisterReceiveInformation($request->email));

        dispatch($job);

        return $this->sendSuccess(
            __('messages.send_email_successfully')
        );
    }
}

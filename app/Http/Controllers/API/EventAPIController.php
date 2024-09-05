<?php

namespace App\Http\Controllers\API;

use App\Contract\CommonBusiness;
use App\Http\Requests\API\CreateEventAPIRequest;
use App\Http\Requests\API\UpdateEventAPIRequest;
use App\Models\Event;
use App\Repositories\EventRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\EventResource;
use App\Models\Course;
use App\Models\EventStudent;
use App\Models\User;
use Carbon\Carbon;
use Response;

/**
 * Class EventController
 * @package App\Http\Controllers\API
 */

class EventAPIController extends AppBaseController
{
    /** @var  EventRepository */
    private $eventRepository;

    use CommonBusiness;

    public function __construct(EventRepository $eventRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        
        $this->eventRepository = $eventRepo;
    }

    /**
     * Display a listing of the Event.
     * GET|HEAD /events
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $events = $this->eventRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            EventResource::collection($events),
            __('messages.retrieved', ['model' => __('models/events.plural')])
        );
    }

    /**
     * Store a newly created Event in storage.
     * POST /events
     *
     * @param CreateEventAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEventAPIRequest $request)
    {
        $user = auth()->user();

        // dd($user->id);

        $request->request->add(['create_by' => $user->id]);

        $input = $request->all();

        if(($user->hasRole(['employee']) && $user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) || $user->hasRole('admin')) {

            $event = $this->eventRepository->create($input);

            if(isset($request->course_id) && $input["course_id"] != NULL) {

                $this->eventRepository->addStudentToEvent($event, $request->course_id);
    
            } else {

                $this->eventRepository->addStudentToEvent($event, null);

            }

        }else {

            $event = $this->eventRepository->create($input);

            if(isset($request->course_id) && $input["course_id"] != NULL) {

                $this->eventRepository->addStudentToEvent($event, $request->course_id);
    
            } else {

                $this->eventRepository->addStudentToEvent($event, null);

            }
            
        }
        
        return $this->sendResponse(
            new EventResource($event),
            __('messages.saved', ['model' => __('models/events.singular')])
        );
    }

    /**
     * Display the specified Event.
     * GET|HEAD /events/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/events.singular')])
            );
        }

        return $this->sendResponse(
            new EventResource($event),
            __('messages.retrieved', ['model' => __('models/events.singular')])
        );
    }

    /**
     * Update the specified Event in storage.
     * PUT/PATCH /events/{id}
     *
     * @param int $id
     * @param UpdateEventAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEventAPIRequest $request)
    {
        $input = $request->all();

        // dd($input);

        // dd($id);
        
        $event = $this->eventRepository->find($id);

        // dd($event);

        if (empty($event)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/events.singular')])
            );
        }
        // if(isset($request->course_id)){
        //     $course_id = $request->course_id;
        //     $this->eventRepository->addStudentToEvent($id, $course_id);
        //     $course = Course::where('course_id', $course_id)->first();
        //     if(!$course){
        //         return $this->sendError(
        //             __('messages.not_found', ['model' => __('models/courses.plural')])
        //         );
        //     }
        // }else {
        //     return $this->sendError(
        //         __('messages.not_found', ['model' => __('models/courses.plural')])
        //     );
        // }
        // $now = date(Carbon::now());
        // if($request->time_start <= $now) {
        //     return $this->sendError(
        //         __('messages.time_start_invalid_format'), 422
        //     );
        // }
        $event = $this->eventRepository->update($input, $id);

        // dd($request->course_id);

        // dd($input["course_id"]);

        if(isset($request->course_id) && $input["course_id"] != NULL) {

            $this->eventRepository->addStudentToEvent($event, $request->course_id);
            
        }
        return $this->sendResponse(
            new EventResource($event),
            __('messages.updated', ['model' => __('models/events.singular')])
        );
    }

    /**
     * Remove the specified Event from storage.
     * DELETE /events/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/events.singular')])
            );
        }

        $event->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/events.singular')])
        );
    }

    public function getListEvent()
    {
        $user = auth()->user();
        $params = request()->query();
     
        $list_events = $this->eventRepository->getListEventUser($user->id, $params);

        // dd($list_events);

        return $this->sendResponse(
            $list_events,
            __('messages.get_list_event_successfully')
        );
    }

    public function getListEventsApproved()
    {
        $user = auth()->user();

        $role = $this->checkRoleForUser($user);

        // dd($role);
        // dd($user->events)

        if ($role != 'admin') {

            // dd('1111');

            $list_events = $this->eventRepository->getListAllEventsApproved($user->id);

        }else {

            // dd('222222');

            $list_events = $this->eventRepository->getListEventsAdmin($user->id);

        }

        return $this->sendResponse(
            $list_events,
            __('messages.get_list_event_successfully')
        );
    }

    public function approvedEvent(Request $request)
    {
        $user = auth()->user();

        $input = $request->all();
       
        if(isset($request->event_id)){

            $event_id = $request->event_id;

            $event = Event::where('id', $event_id)->first();

            if(!$event){

                return $this->sendError(
                    __('messages.not_found', ['model' => __('models/events.singular')])
                );

            }

        } else {

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/events.singular')])
            );

        }

        $user_events = EventStudent::where('event_id', $request->event_id)->where('user_id', $user->id)->first();

        if(!$user_events){

            return $this->sendError(
                __('messages.not_found_user_in_event')
            );

        }

        if(!isset($request->status)){

            return $this->sendError(
                __('messages.status_event_not_empty')
            );
        }

        $events = $this->eventRepository->UserApprovedEvent($input,$user->id);

        return $this->sendResponse(
            $events,
            __('messages.approved_event_successfully')
        );
    }

    public function removeEvent(Request $request) {

        $user = auth()->user();

        $input = $request->all();

        // dd($input['event_id']);

        $event = $this->eventRepository->find($input['event_id']);

        // dd($event->create_by);

        if ($user->id != $event->create_by) {

            return $this->sendError(
                __('messages.errors.not_permission'), 403
            );
        }

        $this->eventRepository->removeEvent($event);

        $event->delete();

        return $this->sendResponse(
            $input['event_id'],
            __('messages.deleted', ['model' => __('models/events.fields.event')])
        );

    }
}

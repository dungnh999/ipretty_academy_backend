<?php

namespace App\Repositories;

use App\Jobs\PushNotificationWhenAddStudentEvent;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Event;
use App\Models\EventStudent;
use App\Repositories\BaseRepository;

/**
 * Class EventRepository
 * @package App\Repositories
 * @version October 25, 2021, 4:09 pm +07
*/

class EventRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'title',
        'description',
        'course_id',
        'time_start',
        'create_by',
        'distance_time_reminder',
        'distance_time_reminder_2'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Event::class;
    }

    public function addStudentToEvent($event, $course_id = null, $isEdit = false) 
    {

        // dd($course_id);
        $user = auth()->user();

        if($course_id != null) {

            $course = Course::find($course_id); 

            $leaders = $course->leaders->pluck('leader_id')->toArray();

            $get_list_student = CourseStudent::where('course_id', $course_id)->get()->pluck('student_id')->toArray();

            $memberIds = array_unique(array_merge($get_list_student, [$course->teacher_id], $leaders));

            if(count($memberIds) > 0) {

                foreach($memberIds as $studentId){

                    $new_event = EventStudent::where('event_id', $event->id)->where('user_id', $studentId)->first();
                    // var_dump($event);

                    if (!$new_event) {

                        EventStudent::create([
                            "event_id" => $event->id,
                            "user_id" => $studentId,
                            "status" => $user->id == $studentId ? 'approved' : 'pending'
                        ]);

                        $job = (new PushNotificationWhenAddStudentEvent($studentId, $event, null));

                        dispatch($job);
                    }

                    // $event_student = EventStudent::updateOrInsert([
                    //     "event_id" => $event->id,
                    //     "user_id" => $student->student_id
                    // ]);

                    // $event_student = new EventStudent();

                    // $event_student->user_id = $student->student_id;

                    // $event_student->event_id = $event->id;

                    // $event_student->save();




                    // event(new \App\Events\PushNotification($event_student->user_id));

                }
            }
        } else {

            // dd($event);

            $new_event = Event::where('id', $event->id)->first();

            // dd($new_event);

            $event_student = EventStudent::updateOrInsert([
                "event_id" => $new_event->id,
                "user_id" => $new_event->create_by
            ], [
                'status' => 'approved'
            ]);

            // $event_student = new EventStudent();

            // $event_student->user_id = $new_event->create_by;

            // $event_student->event_id = $new_event->id;

            // $event_student->status = "approved";
            
            // $event_student->save();
        }
       
    }

    public function getListEventUser($user_id, $params = null)
    {
        $list_events = EventStudent::where('user_id', $user_id)
                                    ->where('event_students.status', 'pending')
                                    ->join('events', 'events.id', 'event_students.event_id')
                                    ->join('courses', 'courses.course_id','events.course_id');
        if (isset($params['event_ids']) && $params['event_ids'] != null) {
            $event_ids = explode(",", $params['event_ids']);
            $list_events = $list_events->whereIn("events.id", $event_ids);
        }
        $list_events = $list_events->select('events.id','title','status','color','time_start','time_end','courses.course_name', 'events.created_at')
        ->orderBy('events.created_at', 'desc')
        ->get();
        return $list_events;
    }

    public function getListAllEventsApproved($user_id)
    {
        // $list_events = EventStudent::where('user_id', $user_id)
        //                             ->where('event_students.status', '=', 'approved')
        //                             ->join('events', 'events.id', 'event_students.event_id')
        //                             ->with('course:courses.course_id,course_name')
        //                             ->orderBy('time_start')  
        //                             ->select('event_id', 'events.id','title','status','color','time_start','time_end', 'events.create_by', 'events.description', 'events.distance_time_reminder', 'events.distance_time_reminder_2')
        //                             ->get();
        $list_events = Event::where(function ($q) use ($user_id){
            $q->orWhere('create_by', $user_id)
            ->orwhereHas('eventStudent', function ($q) use ($user_id) {
                $q->where('user_id', $user_id)
                ->where('status', 'approved');
            });
        })->with('course:courses.course_id,course_name')->orderBy('time_start')->get();
        
    //    dd($list_events);
        return $list_events;
    }

    public function UserApprovedEvent($request, $user_id)
    {

        $event = EventStudent::where('event_id', $request['event_id'])->where('user_id', $user_id)->first();

        // if($request['status'] != 'approved' && $request['status'] != 'cancel') 
        // {
        //     $status = 'approved';
        // }else {
        //     $status = $request['status'];
        // }

        $status = $request['status'];
        
        $event->status = $status;

        $event->save();
    } 

    public function getListEventsAdmin($user_id) 
    {
        $list_events = Event::where('create_by', $user_id)
                            ->orderBy('time_start')
                            ->with('course')  
                            ->get();
        return $list_events;
    }

    public function removeEvent($event) {

        $event_student = $event->eventStudent();

        $event_student->delete();

    }

    public function createEventStudent ($user_id, $events):void {
        // $events = $course->events;
        if (count($events)) {
            foreach ($events as $key => $event) {
                # code...
                EventStudent::create([
                    'user_id' => $user_id,
                    'event_id' => $event->id,
                ]);

                $job = (new PushNotificationWhenAddStudentEvent($user_id, $event, null));

                dispatch($job);
            }
        }
    }

    public function deleteEventStudent ($user_id, $course) {
        $eventStudents = $course->eventStudents($user_id);
        if (count($eventStudents)) {
            foreach ($eventStudents as $key => $eventStudent) {
                $eventStudent->delete();
            }
        }
    }
}

<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\BaseRepository;
use DateInterval;
use DateTime;
use Carbon\Carbon;


/**
 * Class NotificationRepository
 * @package App\Repositories
 * @version November 18, 2021, 2:29 pm +07
*/

class NotificationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type',
        'data',
        'read_at'
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
        return Notification::class;
    }

    public function getAll($params = null)
    {
        $user = auth()->user();
        $now = new DateTime();
        $date = $now->sub(new DateInterval('P15D'));
        $noti_unread = $this->model->where('notifiable_id', $user->id)->whereNull('read_at')->where('created_at', '>=', $date)->get();
        $count_unread = count($noti_unread);
        $noti_unchecked = $this->model->where('notifiable_id', $user->id)->where('checked', false)->where('created_at', '>=', $date)->get();
        $count_unchecked = count($noti_unchecked);
        $notify_data = $this->model->where('notifiable_id', $user->id)->orderBy('created_at', 'desc')->where('created_at', '>=', $date)->select('*');


        if (isset($params['paging']) && $params['paging'] == true) {
            if (isset($params['perpage']) && $params['perpage'] != null) {
                $perpage = $params['perpage'];

                $model = $notify_data->paginate($perpage);
            } else {
                $model = $notify_data->paginate(PERPAGE);
            }
        } else {
            $model = $notify_data->get();
        }




        if(count($model) > 0 ) {
            foreach($model as $noti) {
                $noti['info'] = json_decode($noti['data'], true);
                $targetTime = Carbon::parse($noti['updated_at']);
                $currentTime = Carbon::now();
                $timeDifference = $targetTime->diff($currentTime);
//                if ($timeDifference->days > 0) {
//                    $noti['time_notification'] =  $timeDifference->days . " ngày ";
//                }
//                if ($timeDifference->h > 0) {
//                    $noti['time_notification'] = $timeDifference->h . " giờ";
//                }
                $noti['avatar'] = ($noti['info']['avatar'] == "") ? $this->generateAvatar('NO') : $noti['info']['avatar'];
            }
        }
//        if (isset($count_unread)) {
//          $customField = collect(['count_unread' => $count_unread]);
//          $model = $customField->merge($model);
//        }
//        if (isset($count_unchecked)) {
//            $customField = collect(['count_unchecked' => $count_unchecked]);
//            $model = $customField->merge($model);
//        }

        return $model;
    }

    public function setCheckStatusTheNotifications()
    {
        $user = auth()->user();
        $now = new DateTime();
        $date = $now->sub(new DateInterval('P15D'));
        $noti_unreads = $this->model->where('notifiable_id', $user->id)->where('checked', false)->where('created_at', '>=', $date)->get();
        foreach($noti_unreads as $noti) {
            $noti->checked = true;
            $noti->save();
        }
    }

    public function readAllNotifications($input)
    {
        $noti = $this->model->where('id', $input['id'])->first();
        $now = new DateTime();
        $noti->read_at = $now;
        $noti->checked = true;
        $noti->save();
    }
}

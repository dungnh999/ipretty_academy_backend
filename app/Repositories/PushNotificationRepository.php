<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\PushNotification;
use App\Repositories\BaseRepository;

/**
 * Class PushNotificationRepository
 * @package App\Repositories
 * @version November 23, 2021, 11:45 am +07
*/

class PushNotificationRepository extends BaseRepository
{
    use CommonBusiness;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notification_title',
        'notification_cat',
        'group_receivers',
        'notification_message'
    ];

    protected $relations = ['createdBy'];

    protected $relationSearchable = [
        'name'
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
        return PushNotification::class;
    }

    public function allNotifications($params) {
        $query = $this->model->newQuery()
        ->with('createdBy', function ($q) {
            $q->select('name', 'email', 'id');
        })
        ->orderBy('created_at', 'desc');

        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $query = $query->whereIn('isPublished', $status);
        }

        if (isset($params['created_at']) && $params['created_at'] != null) {
            $created_at = $params['created_at'];
            $query = $query->whereDate('created_at', '>=', $created_at);
        }

        if (isset($params['updated_at']) && $params['updated_at'] != null) {
            $updated_at = $params['updated_at'];
            $query = $query->whereDate('updated_at', '>=', $updated_at);
        }

        if (isset($params['receivers']) && $params['receivers'] != null) {
            $receivers = explode(',', $params['receivers']);
            $whereids = "group_receivers";
            $str = "";
            $i = 1; // to append AND in query

            foreach ($receivers as $receiver) {
                $str .= "FIND_IN_SET( '$receiver', $whereids)";
                if ($i < count($receivers)) {
                    $str .= " OR "; // use OR as per use
                }
                $i++;
            }
            $query = $query->whereRaw($str);
        }

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
        }

        // $model = $query->paginate(PERPAGE);
        if (isset($params['paging']) && $params['paging'] == true) {
            if (isset($params['perpage']) && $params['perpage'] != null) {

                $perpage = $params['perpage'];

                $model = $query->paginate($perpage);
            } else {
                $model = $query->paginate(PERPAGE);
            }
        } else {
            $model = $query->get();
        }

        return $model;
    }
}

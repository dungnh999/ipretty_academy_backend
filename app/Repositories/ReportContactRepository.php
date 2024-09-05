<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Jobs\PushNotificationWhenReportError;
use App\Models\ReportContact;
use App\Models\User;
use App\Notifications\SendReportError;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Notification;

/**
 * Class ReportContactRepository
 * @package App\Repositories
 * @version November 18, 2021, 10:15 am +07
*/

class ReportContactRepository extends BaseRepository
{
    use CommonBusiness;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'report_title',
        'report_content',
        'attachment',
        'reporter',
        'isReport',
        'isSended'
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
        return ReportContact::class;
    }

    public function createReport($input, $request) {

        if ($input['isReport']) {

            $user = auth()->user();

            $input["reporter_id"] = $user->id;

        }

        $model = $this->model->newInstance($input);

        $model->save();

        if ($request->file('attachments')) {

            $model->handleMedia($request);

        }

        // dd($input['isReport']);

        if ($input['isReport']) {

            $user = auth()->user();

            if ($this->checkRoleForUser($user) == 'admin') {

                $supports_team = User::withTrashed()->where('support_team', 1)->get();

                if (count($supports_team)) {

                    foreach($supports_team as $support_team) {

                        if($support_team)
                        { 

                            $support_team->notify(new SendReportError($support_team, $model, $user->email));

                        }

                    }

                }
                
            } else {

                $admins = $this->getUserByMultipleRole('admin');

                if(count($admins) > 0 ){

                    foreach($admins as $admin) {

                        if($admin)
                        { 

                            $admin->notify(new SendReportError($admin, $model));

                        }

                    }

                }

            }

            // dd($model);

            $job = (new PushNotificationWhenReportError($model, $user));

            dispatch($job);

            // $this->pushNotificationForUser('admin');

        } else {

            $admins = $this->getUserByMultipleRole('admin');

            if(count($admins) > 0 ){

                foreach($admins as $admin) {

                    if($admin)
                    { 

                        $admin->notify(new SendReportError($admin, $model));

                    }

                }

            }

            $job = (new PushNotificationWhenReportError($model));

            dispatch($job);

            // $this->pushNotificationForUser('admin');

        }

       

        return $model;
    }
}

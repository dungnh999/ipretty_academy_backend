<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;
use Spatie\Permission\Models\Role as ModelsRole;

class DashboardAPIController extends AppBaseController
{
    public function __construct() {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });

    }

    public function overviewData () {

        $timeCurent = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh');
        $currentMonth = date("Y-m-t", strtotime($timeCurent));
        
        $previousDate = $timeCurent->subMonth();
        $previousMonth = date("Y-m-t", strtotime($previousDate));
        
        $prevPreviousDate = $previousDate->subMonth(1);
        $prevPreviousMonth = date("Y-m-t", strtotime($prevPreviousDate));
        // dd($previousMonth);

        $overview_courses = Course::selectRaw('
        COUNT(course_id) as total_courses,
        COUNT(CASE WHEN DATE(courses.created_at) <= DATE("' . $currentMonth .'") AND DATE(courses.created_at) > DATE("' . $previousMonth . '") THEN 1 ELSE NULL END) as total_courses_current,
        COUNT(CASE WHEN DATE(courses.created_at) <= DATE("' . $previousMonth .'") AND DATE(courses.created_at) > DATE("' . $prevPreviousMonth . '") THEN 1 ELSE NULL END) as total_courses_prev,
        COUNT(CASE WHEN course_type = "Local" THEN 1 ELSE NULL END) AS local_courses,
        COUNT(CASE WHEN course_type = "Business" THEN 1 ELSE NULL END) AS free_courses,
        COUNT(CASE WHEN course_type = "Group" THEN 1 ELSE NULL END) AS group_courses')->where('is_published', 1)->first();

        $overview_courses->fluctuating = -1;

        $fluctuating = $overview_courses->total_courses_current - $overview_courses->total_courses_prev;

        if ($fluctuating > 0) {
            
            $overview_courses->fluctuating = 1;

            $overview_courses->rate = $overview_courses->total_courses_prev ? Round($fluctuating * 100 / $overview_courses->total_courses_prev, 2) : 100;

        }else if ($fluctuating == 0) {

            $overview_courses->fluctuating = 0;

            $overview_courses->rate = 0;

        } else {

            $overview_courses->rate = $overview_courses->total_courses_prev ? Round((-$fluctuating) * 100 / $overview_courses->total_courses_prev, 2) : 100;

        }

        $memberByRoles = ModelsRole::where(function($q) {
            $q->where(function ($w) {
                $w->orwhere('roles.name', 'user')
                    ->orwhere('roles.name', 'employee');
            });
        // })->select('name')->withCount(['users', 'users as users_count' => function($w) {
        //     $w->whereNotNull('users.created_at');
        // }])->get()->pluck('users_count', 'name')->toArray();
        })->select('name')->withCount(['users'])->get()->pluck('users_count', 'name')->toArray();

        $overview_members = User::whereHas('roles', function($q) {
            $q->where(function ($w) {
                $w->orwhere('roles.name', 'user')
                    ->orwhere('roles.name', 'employee');
            });
        })
        ->selectRaw('
        COUNT(CASE WHEN users.created_at IS NOT NULL then 1 else NULL end) as total_members,
        COUNT(CASE WHEN DATE(users.created_at) <= DATE("' . $currentMonth . '") AND DATE(users.created_at) > DATE("' . $previousMonth . '") THEN 1 ELSE NULL END) as total_members_prev,
        COUNT(CASE WHEN DATE(users.created_at) <= DATE("' . $previousMonth . '") AND DATE(users.created_at) > DATE("' . $prevPreviousMonth . '") THEN 1 ELSE NULL END) as total_members_prev_2month
        ')->first();
        // dd($overview_members);

        $response_overview_members['total_members'] = $overview_members->total_members;
        $member_fluctuating = $overview_members->total_members_prev - $overview_members->total_members_prev_2month;
        // dd($overview_members->total_members_prev);
        // dd($previousMonth);
        $response_overview_members['fluctuating'] = -1;
        if ($member_fluctuating > 0) {
            $response_overview_members['fluctuating'] = 1;
            $response_overview_members['rate'] = $member_fluctuating;

        } else if ($member_fluctuating == 0){
            $response_overview_members['fluctuating'] = 0;
            $response_overview_members['rate'] = 0;
        } else {
            $response_overview_members['rate'] = -$member_fluctuating;
        }

        $response_overview_members['members'] = (object)$memberByRoles;

        $certificates_overview = CourseStudent::where('isPassed', 1)
        ->selectRaw('
            COUNT(CASE WHEN isPassed = 1 then 1 else NULL end) as total_certificates,
            COUNT(CASE WHEN isPassed = 1 AND DATE(courses_students.completed_at) <= DATE("' . $currentMonth . '") AND DATE(courses_students.completed_at) > DATE("' . $previousMonth . '") THEN 1 ELSE NULL END) as total_certificates_prev,
            COUNT(CASE WHEN isPassed = 1 AND DATE(courses_students.completed_at) <= DATE("' . $previousMonth . '") AND DATE(courses_students.completed_at) > DATE("' . $prevPreviousMonth . '") THEN 1 ELSE NULL END) as total_certificates_prev_2month
        ')->first();

        $certificates_by_user = CourseStudent::where('isPassed', 1)->whereHas('student', function($q) {
            $q->whereHas('roles', function ($w) {
                $w->where('name', 'user');
            });
        })->with('student')->get()->count();

        $certificates_by_employee = CourseStudent::where('isPassed', 1)->whereHas('student', function($q) {
            $q->whereHas('roles', function ($w) {
                $w->where('name', 'employee');
            });
        })->with('student')->get()->count();

        $response_overview_certificates['total_certificates'] = $certificates_overview->total_certificates;

        $certificates_fluctuating = $certificates_overview->total_certificates_prev - $certificates_overview->total_certificates_prev_2month;

        $response_overview_certificates['fluctuating'] = -1;

        if ($certificates_fluctuating > 0) {
            $response_overview_certificates['fluctuating'] = 1;
            $response_overview_certificates['rate'] = $certificates_fluctuating;

        } else if ($member_fluctuating == 0) {
            $response_overview_certificates['fluctuating'] = 0;
            $response_overview_certificates['rate'] = 0;

        } else {
            $response_overview_certificates['rate'] = -$certificates_fluctuating;
        }


        // dd($certificates_by_role);
        $certificates_by_roles['user'] = $certificates_by_user;
        $certificates_by_roles['employee'] = $certificates_by_employee;

        $response_overview_certificates['members'] = (object)$certificates_by_roles;

        $overviewTransactions = Transaction::where('transactions.status', 'approved')
        ->selectRaw('
        SUM(orders.grandTotal) as total_transactions,
        SUM(CASE WHEN transactions.payment_method = "banking" THEN orders.grandTotal ELSE NULL END) as total_transaction_banking,
        SUM(CASE WHEN transactions.payment_method = "at_company" THEN orders.grandTotal ELSE NULL END) as total_transaction_company,
        SUM(CASE WHEN DATE(transactions.confirmed_at) <= DATE("' . $currentMonth . '") AND DATE(transactions.confirmed_at) > DATE("' . $previousMonth . '") THEN orders.grandTotal ELSE NULL END) as total_transaction_current,
        SUM(CASE WHEN DATE(transactions.confirmed_at) <= DATE("' . $previousMonth . '") AND DATE(transactions.confirmed_at) > DATE("' . $prevPreviousMonth . '") THEN orders.grandTotal ELSE NULL END) as total_transaction_prev
        ')
        ->join('orders', 'orders.order_id', '=', 'transactions.order_id')->first();

        // dd($overviewTransactions);

        $overviewTransactions->fluctuating = -1;

        $fluctuating = $overviewTransactions->total_transaction_current - $overviewTransactions->total_transaction_prev;

        if ($fluctuating > 0) {

            $overviewTransactions->fluctuating = 1;

            $overviewTransactions->rate = $overviewTransactions->total_transaction_prev ? Round($fluctuating * 100 / $overviewTransactions->total_transaction_prev, 2) : 100;
        } else if ($fluctuating == 0) {

            $overviewTransactions->fluctuating = 0;

            $overviewTransactions->rate = 0;
        } else {

            $overviewTransactions->rate = $overviewTransactions->total_transaction_prev ? Round((-$fluctuating) * 100 / $overviewTransactions->total_transaction_prev, 2) : 100;
        }

        $response['overview_course'] = $overview_courses;
        $response['overview_member'] = $response_overview_members;
        $response['overview_certificates'] = $response_overview_certificates;
        $response['overviewTransactions'] = $overviewTransactions;

        return $this->sendResponse(
            $response,
            __('messages.overview_data_retrieved')
        );
    }
}

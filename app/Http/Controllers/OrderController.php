<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends AppBaseController
{

  private $orderRepository;

  public function __construct(OrderRepository $orderRepository)
  {
    $this->middleware(function ($request, $next) {

      $user = auth()->user();
      if ($user) {
        \App::setLocale($user->lang);
      }
      return $next($request);
    });
    $this->orderRepository = $orderRepository;
  }


  function index(Request $request){
    return view('contents.order.index');
  }

  function getDataOrder(Request $request){
    $dataOrder = $this->orderRepository->getDataOrder();
    $collect = collect($dataOrder);
    $dataOrderCheckedout = $collect->where('status', 'checkedout')->All();
    $dataOrderPaid = $collect->where('status', 'paid')->All();
    $dataOrderOrdered = $collect->where('status', 'ordered')->All();
    $dataOrderCanceled = $collect->where('status', 'canceled')->All();


    // DataTable
    $dataTableOrderCheckedout = $this->drawDataTableOrder($dataOrderCheckedout);
    $dataTableOrderPaid = $this->drawDataTableOrder($dataOrderPaid);
    $dataTableOrderOrderd = $this->drawDataTableOrder($dataOrderOrdered);
    $dataTableOrderCanceled = $this->drawDataTableOrder($dataOrderCanceled);

    // DataTotal
    $dataTotal = [
      'total-checkouted' =>  count($dataOrderCheckedout),
      'total-paid' =>  count($dataOrderPaid),
      'total-ordered' =>  count($dataOrderOrdered),
      'total-canceled' =>  count($dataOrderCanceled)
    ];
    return [$dataTotal, $dataTableOrderCheckedout, $dataTableOrderPaid , $dataTableOrderOrderd, $dataTableOrderCanceled];
  }


  private function drawDataTableOrder($data)
  {
    return Datatables::of($data)
      ->addColumn('action', function ($row) {
        return '<div class="d-inline-block text-nowrap" >
                      <button class="btn btn-icon btn-outline-primary rounded-pill btn-sm" data-id="" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-info-circle"></i>
                      </button>
                  </div>';        
      })
      ->addColumn('name', function ($row) {
        $data = $row->getRelation('createdBy');
        $name = $data['name'];
        $email = $data['email'];
        $avatar = ($data['avatar'] != null) ? $data['avatar'] : '';
        return $this->getNameAvatarDataTable($name,$avatar,$email);
    })
      ->addColumn('total', function ($row) {
          return $this->formatVND($row['total']);
      })
      ->addColumn('created_at', function ($row) {
        return $this->formartDateTime($row['created_at']);
      })
      ->addIndexColumn()
      ->rawColumns(['action', 'name'])
      ->make(true);
  }
}

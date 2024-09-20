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

    // DataTable
    $dataTableOrderCheckedout = $this->drawDataTableOrder($dataOrderCheckedout);


    // DataTotal
    $dataTotal = [
      'total-checkouted' =>  count($dataOrderCheckedout)
    ];
    return [$dataTotal, $dataTableOrderCheckedout];
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
      ->addColumn('total', function ($row) {
          return $this->formatVND($row['total']);
      })
      ->addColumn('created_at', function ($row) {
        return $this->formartDateTime($row['created_at']);
      })
      ->addIndexColumn()
      ->rawColumns(['action'])
      ->make(true);
  }
}

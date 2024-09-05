<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
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

  }


  private function drawDataTableOrder($data)
  {
    return Datatables::of($data)
      ->addColumn('action', function ($row) {
        $departmentId = $row['category_id'];
        if ($row['isPublished']) {
          return '<div class="d-inline-block text-nowrap" >
                      <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                      <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="changeStatusUnActiveCoursesCategory($(this))">
                          <i class="bx bx-x"></i>
                      </button>
                  </div>';
        } else {
          return '<div class="d-inline-block text-nowrap" >
                       <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                       <button class="btn btn-icon btn-outline-success rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="changeStatusActiveCoursesCategory($(this))">
                          <i class="bx bx-check"></i>
                      </button>
                  </div>';
        }
      })
      ->addIndexColumn()
      ->rawColumns(['action'])
      ->make(true);
  }
}

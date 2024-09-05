<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PaymentController extends Controller
{

  private $transactionRepository;
  private $orderRepository;

  public function __construct(TransactionRepository $transactionRepo, OrderRepository $orderRepository)
  {
    $this->middleware(function ($request, $next) {
      $this->user = auth()->user();
      if ($this->user) {
        \App::setLocale($this->user->lang);
      }
      return $next($request);
    });
    $this->transactionRepository = $transactionRepo;
    $this->orderRepository = $orderRepository;
  }


  public function createPayment(Request $request)
  {
    $vnp_TxnRef = $request->get('order_id'); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
    $vnp_Amount = $request->input('amount') * 100;
    $vnp_Locale = 'vn';
    $vnp_IpAddr = request()->ip();

    $vnp_TmnCode = env('VNPAY_TMNCODE'); //Mã định danh merchant kết nối (Terminal Id)
    $vnp_HashSecret = env('VNPAY_HASHSECRET'); //Secret key
    $vnp_Url = env('VNPAY_URL');
    $vnp_Returnurl = env('VNPAY_CALLBACK');

    //Expire
    $startTime = date("YmdHis");
    $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
    $inputData = array(
      "vnp_Version" => "2.1.0",
      "vnp_TmnCode" => $vnp_TmnCode,
      "vnp_Amount" => $vnp_Amount,
      "vnp_Command" => "pay",
      "vnp_CreateDate" => date('YmdHis'),
      "vnp_CurrCode" => "VND",
      "vnp_IpAddr" => $vnp_IpAddr,
      "vnp_Locale" => $vnp_Locale,
      "vnp_OrderInfo" => $vnp_TxnRef,
      "vnp_OrderType" => "other",
      "vnp_ReturnUrl" => $vnp_Returnurl,
      "vnp_TxnRef" => $vnp_TxnRef,
      "vnp_ExpireDate" => $expire
    );

    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
      $inputData['vnp_BankCode'] = $vnp_BankCode;
    }
    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
      if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
      } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
      }
      $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $vnp_Url = $vnp_Url . "?" . $query;
    if (isset($vnp_HashSecret)) {
      $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
      $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    }
    return response()->json([
      'vnp_Url' => $vnp_Url,
      'requestData' => $inputData,
    ]);
  }
}

<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;


class VnpayController extends Controller
{

  private $orderRepository;
  private $transactionRepository;

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


  public function handleVNPayCallback(Request $request)
  {
    $vnp_HashSecret = "ELFLWDDLCRVIXRDAOXHYYFPPPOGWOCHP"; // Secret key

    // Lấy các tham số từ VNPay
    $vnp_ResponseCode = $request->input('vnp_ResponseCode');
    $vnp_SecureHash = $request->input('vnp_SecureHash');
    $inputData = $request->except('vnp_SecureHash');

    // Tạo chuỗi hash từ dữ liệu nhận được
    ksort($inputData);
    $hashdata = "";
    foreach ($inputData as $key => $value) {
      $hashdata .= urlencode($key) . '=' . urlencode($value) . '&';
    }
    $hashdata = rtrim($hashdata, '&');
    $vnp_ComputeHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $user = auth()->user();    // Kiểm tra mã phản hồi và hash
    if ($vnp_ResponseCode == '00' && $vnp_SecureHash == $vnp_ComputeHash) {
      // Thanh toán thành công
      $order = $this->orderRepository->find($request->get('vnp_TxnRef'));
      if ($order) {
        $this->orderRepository->updateStatus($order->id, "paid");

        $this->transactionRepository->create([
          'order_id' => $order->id,
          'user_id' => $user->id,
          'payment_method' => 'VNPAY',
          // Các thông tin khác
        ]);
      }
      return redirect()->to('http://localhost:3000/pay-refund?order_id=' . $order->id); // Chuyển hướng đến trang thành công với thông tin đơn hàng
    } else {
      // Thanh toán thất bại
      return redirect()->to('/payment/failure'); // Chuyển hướng đến trang thất bại
    }
  }

}

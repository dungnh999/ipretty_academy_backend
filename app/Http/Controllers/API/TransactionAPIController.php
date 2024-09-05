<?php

namespace App\Http\Controllers\API;

use App\Exports\StatistialBusinessExport;
use App\Http\Requests\API\CreateTransactionAPIRequest;
use App\Http\Requests\API\UpdateTransactionAPIRequest;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\ApproveOrRejectTransactionAPIRequest;
use App\Http\Resources\TransactionResource;
//use App\Jobs\AddMemberIntoEvent;
use App\Models\Course;
use App\Repositories\CartItemRepository;
use App\Repositories\CourseStudentRepository;
use App\Repositories\LearningProcessRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CourseRepository;
use Response;
use Carbon\Carbon;

/**
 * Class TransactionController
 * @package App\Http\Controllers\API
 */

class TransactionAPIController extends AppBaseController
{
    /** @var  TransactionRepository */
    private $transactionRepository;
    private $orderRepository;
    private $courseStudentRepository;
    private $orderItemRepository;
    private $cartItemRepository;
    private $user;
    private $courseRepository;

    public function __construct(TransactionRepository $transactionRepo, OrderRepository $orderRepository,
    CourseStudentRepository $courseStudentRepository, LearningProcessRepository $learningProcessRepository, OrderItemRepository $orderItemRepository,
    CartItemRepository $cartItemRepository, CourseRepository $courseRepository)
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
        $this->courseStudentRepository = $courseStudentRepository;
        $this->learningProcessRepository = $learningProcessRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Display a listing of the Transaction.
     * GET|HEAD /transactions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $params = request()->query();

        $transactions = $this->transactionRepository->allTransactions($params);

        return $this->sendResponse(
            $transactions,
            __('messages.retrieved', ['model' => __('models/transactions.plural')])
        );
    }

    /**
     * Store a newly created Transaction in storage.
     * POST /transactions
     *
     * @param CreateTransactionAPIRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $order_id = $request->order_id;

        $user = auth()->user();

        $order = $user->orderById($order_id);
        // $order = $this->orderRepository->checkOrderOfUser($order_id, $user_id);

        if (empty($order)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orders.singular')])
            );
        }

        if ($order->status != 'ordered') {
            return $this->sendError(
                __('messages.order_used')
            );
        }

        $input = $request->all();

        $input['user_id'] = $user->id;

        $transaction = $this->transactionRepository->create($input);

        if ($transaction) {

            $order->status = 'checkedout';
            $order->save();

        }

        return $this->sendResponse(
            new TransactionResource($transaction),
            __('messages.saved', ['model' => __('models/transactions.singular')])
        );
    }

    /**
     * Display the specified Transaction.
     * GET|HEAD /transactions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->find($id);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        return $this->sendResponse(
            new TransactionResource($transaction),
            __('messages.retrieved', ['model' => __('models/transactions.singular')])
        );
    }

    /**
     * Update the specified Transaction in storage.
     * PUT/PATCH /transactions/{id}
     *
     * @param int $id
     * @param UpdateTransactionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTransactionAPIRequest $request)
    {
        $input = $request->all();

        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->find($id);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        $transaction = $this->transactionRepository->update($input, $id);

        return $this->sendResponse(
            new TransactionResource($transaction),
            __('messages.updated', ['model' => __('models/transactions.singular')])
        );
    }

    /**
     * Remove the specified Transaction from storage.
     * DELETE /transactions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->find($id);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        $transaction->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/transactions.singular')])
        );
    }

    public function approveOrRejectTransaction (Request $request) {

        $transaction = $this->transactionRepository->checkTransactionCode($request->email, $request->transaction_code);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        $transaction = $this->transactionRepository->approveOrRejectTransaction($transaction, $request->status);

        $message = __('messages.rejected_order_successfully');
        $oderStatus = 'canceled';
        if ($request->status == 'approved'){
            $message = __('messages.approved_order_successfully');
            $oderStatus = 'paid';
        }

        $updateOrder = $this->orderRepository->updateStatus($transaction->order_id, $oderStatus);
        $itemOrder = $this->orderItemRepository->getItemCourseOrder($transaction->order_id);
      if ($request->status == 'approved') {
            $courseIds = $itemOrder->pluck('course_id')->toArray();
          foreach ($courseIds as $key => $courseId) {

                $input['course_id'] = $courseId;

                $input['student_id'] = $updateOrder->user_id;

                $this->courseStudentRepository->create($input);

                $this->learningProcessRepository->createProcessLearning($input, $updateOrder->user_id);
                $course = $this->courseRepository->find($courseId);
                $events = $course->events;
//                $job = (new AddMemberIntoEvent($updateOrder->user_id, $events));

//                dispatch($job);
            }
        }

        return $this->sendResponse(
            new TransactionResource($transaction),
            $message
        );

    }

    public function checkTransactionCode (Request $request) {

        $transaction = $this->transactionRepository->checkTransactionCode($request->email, $request->transaction_code);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        return $this->sendResponse(
            new TransactionResource($transaction),
            __('messages.retrieved', ['model' => __('models/transactions.singular')])
        );
    }

    public function rejectTransaction ($transaction_id) {

        $transaction = $this->transactionRepository->find($transaction_id);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        $user = auth()->user();
        $transaction = $this->transactionRepository->checkTransactionCode($user->email, $transaction->transaction_code);

        $updateTransaction = $this->transactionRepository->approveOrRejectTransaction($transaction, 'rejected');

        $message = __('messages.rejected_order_successfully');

        return $this->sendResponse(
            new TransactionResource($updateTransaction),
            $message
        );
    }

    public function getTransactionHistories()
    {
        $user = auth()->user();

        $params = request()->query();

        $transactions = $this->transactionRepository->getTransactionHistories($user->id, $params);

        return $this->sendResponse(
            $transactions,
            __('messages.retrieved', ['model' => __('models/users.fields.transaction')])
        );
    }

    public function approveTransaction($transaction_id, Request $request)
    {

        $transaction = $this->transactionRepository->find($transaction_id);

        if (empty($transaction)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/transactions.singular')])
            );
        }

        if ($transaction->status == 'approved') {
            return $this->sendError(
                __('messages.approved_order')
            );
        }else if ($transaction->status == 'rejected') {
            return $this->sendError(
                __('messages.rejected_order')
            );
        }

        $updateTransaction = $this->transactionRepository->approveOrRejectTransaction($transaction, $request->status);

        $message = __('messages.rejected_order_successfully');
        $oderStatus = 'canceled';

        if ($request->status == 'approved') {
            $message = __('messages.approved_order_successfully');
            $oderStatus = 'paid';
        }

        $updateOrder = $this->orderRepository->updateStatus($transaction->order_id, $oderStatus);

        $courseIds = $updateOrder->courses->pluck('course_id')->toArray();

        if ($request->status == 'approved') {
            // dd($courseIds);
            foreach ($courseIds as $key => $courseId) {

                $input['course_id'] = $courseId;

                $input['student_id'] = $updateOrder->user_id;

                $this->courseStudentRepository->create($input);

                $this->learningProcessRepository->createProcessLearning($input, $updateOrder->user_id);

                $course = $this->courseRepository->find($courseId);
                $events = $course->events;
//                $job = (new AddMemberIntoEvent($updateOrder->user_id, $events));
//
//                dispatch($job);
            }
        }else {
            $this->cartItemRepository->removeCartItemByCourseAndUser($courseIds, $updateOrder->user_id);

            $this->orderItemRepository->removeOrderItemCanceled($updateOrder->order_id);
        }

        return $this->sendResponse(
            new TransactionResource($updateTransaction),
            $message
        );
    }


    public function analysisBusiness(Request $request) {
        $params = request()->query();

        $analysisBusiness = $this->transactionRepository->analysisBusiness($params);

        // dd(count((array)$analysisBusiness));

        $new_analysisBusiness = (array) $analysisBusiness;

        // dd(count($new_analysisBusiness));

        if (isset($params["export"]) && $params["export"]) {

            $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . 'statistical_business_export.';

            mkdir(storage_path('/app/' . $folder_file), 0700);

            if (count($new_analysisBusiness)) {

                (new StatistialBusinessExport($new_analysisBusiness))->store($folder_file . '/' . "-statistical-business-export.xlsx");

            }

            return response()->file(storage_path('/app/' . $folder_file . '/' . "-statistical-business-export.xlsx"))->deleteFileAfterSend(true);

        }

        return $this->sendResponse(
            $analysisBusiness,
            __('messages.retrieved', ['model' => __('models/courses.fields.analysisBusiness')])
        );
    }

    public function testRemoveCartitem ($transaction_id, $user_id) {

        $transaction = $this->transactionRepository->find($transaction_id);

        $this->cartItemRepository->removeCartItemByCourseAndUser($transaction->order->courses->pluck('course_id')->toArray(), $user_id);

    }
}

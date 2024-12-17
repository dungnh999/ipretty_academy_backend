<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOrderAPIRequest;
use App\Http\Requests\API\UpdateOrderAPIRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\OrderResource;
use App\Repositories\CartItemRepository;
use App\Repositories\DiscountCodeRepository;
use App\Repositories\UserActivateDiscountCodeRepository;
use Response;

/**
 * Class OrderController
 * @package App\Http\Controllers\API
 */

class OrderAPIController extends AppBaseController
{
    /** @var  OrderRepository */
    private $orderRepository;
    private $discountCodeRepository;
    private $userActivateDiscountCodeRepository;
    private $cartItemRepository;

    public function __construct(OrderRepository $orderRepo, DiscountCodeRepository $discountCodeRepository,
    UserActivateDiscountCodeRepository $userActivateDiscountCodeRepository, CartItemRepository $cartItemRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->orderRepository = $orderRepo;
        $this->discountCodeRepository = $discountCodeRepository;
        $this->userActivateDiscountCodeRepository = $userActivateDiscountCodeRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * Display a listing of the Order.
     * GET|HEAD /orders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $orders = $this->orderRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            OrderResource::collection($orders),
            __('messages.retrieved', ['model' => __('models/orders.plural')])
        );
    }

    /**
     * Store a newly created Order in storage.
     * POST /orders
     *
     * @param CreateOrderAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderAPIRequest $request)
    {
//        dd($request);

        // $input = $request->all();
        $user = auth()->user();
        $course_ids = collect($request->get('data'))->pluck('course_id')->toArray();
        $ordered = $user->coursesOrdered->where('status', 'ordered')->pluck('courses')->toArray();
        $coursesOrderedUser = [];

        foreach ($ordered as $key => $order) {
            foreach ($order as $key => $item) {
                array_push($coursesOrderedUser, $item);
            }
        }


        $orderedCourses = array_filter($coursesOrderedUser, function($courseOrdered) use ($course_ids) {
            return in_array($courseOrdered['course_id'], $course_ids);
        });


//


//
//
//

//

//
//
//        if (count($orderedCourses)) {
//            $orderedCoursesResponse = array_column($orderedCourses, 'course_name');
//            return $this->sendError(
//                __('messages.courses_has_been_bought'), 422,
//                $orderedCoursesResponse
//            );
//        }

//        if (isset($request->discount_code) && $request->discount_code != null) {
//            $used_discount_code = $this->discountCodeRepository->checkUsingDiscountCode($user, $request->discount_code);
//            // dd($used_discount_code);
//            if (!$used_discount_code['isExist']) {
//                return $this->sendError(
//                    __('messages.code_is_not_found')
//                );
//            }else if ($used_discount_code['isUsed']) {
//                return $this->sendError(
//                    __('messages.code_is_used')
//                );
//            }else if ($used_discount_code['isExpired']) {
//                return $this->sendError(
//                    __('messages.code_is_expired')
//                );
//            }else if ($used_discount_code['isOutOfStock']) {
//                return $this->sendError(
//                    __('messages.code_is_out_of_stock')
//                );
//            }
//        }

            $order = $this->orderRepository->createOrderCourse($request, $user);


//        if ($order) {
//            if (isset($request->discount_code) && $request->discount_code != null && $used_discount_code['discountCode'] != null) {
//                $inputUserActiveCode['user_id'] = $user->id;
//                $inputUserActiveCode['discount_code_id'] = $used_discount_code['discountCode']->id;
//                $this->userActivateDiscountCodeRepository->create($inputUserActiveCode);
//            }
//
//            $removeCartItem = $this->cartItemRepository->removeCartItemOrdered($request->cart_id, $course_ids);
//        }



        return $this->sendResponse(
            new OrderResource($order),
            __('messages.saved', ['model' => __('models/orders.singular')])
        );
    }

    /**
     * Display the specified Order.
     * GET|HEAD /orders/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(Request $request)
    {
        /** @var Order $order */
        $order = $this->orderRepository->find($request->get('id'));

        if (empty($order)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orders.singular')])
            );
        }

        return $this->sendResponse(
            new OrderResource($order),
            __('messages.retrieved', ['model' => __('models/orders.singular')])
        );
    }

    /**
     * Update the specified Order in storage.
     * PUT/PATCH /orders/{id}
     *
     * @param int $id
     * @param UpdateOrderAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrderAPIRequest $request)
    {
        $input = $request->all();

        /** @var Order $order */
        $order = $this->orderRepository->find($id);

        if (empty($order)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orders.singular')])
            );
        }

        $order = $this->orderRepository->update($input, $id);

        return $this->sendResponse(
            new OrderResource($order),
            __('messages.updated', ['model' => __('models/orders.singular')])
        );
    }

    /**
     * Remove the specified Order from storage.
     * DELETE /orders/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Order $order */
        $order = $this->orderRepository->find($id);

        if (empty($order)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orders.singular')])
            );
        }

        $order->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/orders.singular')])
        );
    }
}

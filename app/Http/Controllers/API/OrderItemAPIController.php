<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOrderItemAPIRequest;
use App\Http\Requests\API\UpdateOrderItemAPIRequest;
use App\Models\OrderItem;
use App\Repositories\OrderItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\OrderItemResource;
use Response;

/**
 * Class OrderItemController
 * @package App\Http\Controllers\API
 */

class OrderItemAPIController extends AppBaseController
{
    /** @var  OrderItemRepository */
    private $orderItemRepository;

    public function __construct(OrderItemRepository $orderItemRepo)
    {
        $this->orderItemRepository = $orderItemRepo;
    }

    /**
     * Display a listing of the OrderItem.
     * GET|HEAD /orderItems
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $orderItems = $this->orderItemRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            OrderItemResource::collection($orderItems),
            __('messages.retrieved', ['model' => __('models/orderItems.plural')])
        );
    }

    /**
     * Store a newly created OrderItem in storage.
     * POST /orderItems
     *
     * @param CreateOrderItemAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderItemAPIRequest $request)
    {
        $input = $request->all();

        $orderItem = $this->orderItemRepository->create($input);

        return $this->sendResponse(
            new OrderItemResource($orderItem),
            __('messages.saved', ['model' => __('models/orderItems.singular')])
        );
    }

    /**
     * Display the specified OrderItem.
     * GET|HEAD /orderItems/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var OrderItem $orderItem */
        $orderItem = $this->orderItemRepository->find($id);

        if (empty($orderItem)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orderItems.singular')])
            );
        }

        return $this->sendResponse(
            new OrderItemResource($orderItem),
            __('messages.retrieved', ['model' => __('models/orderItems.singular')])
        );
    }

    /**
     * Update the specified OrderItem in storage.
     * PUT/PATCH /orderItems/{id}
     *
     * @param int $id
     * @param UpdateOrderItemAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrderItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var OrderItem $orderItem */
        $orderItem = $this->orderItemRepository->find($id);

        if (empty($orderItem)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orderItems.singular')])
            );
        }

        $orderItem = $this->orderItemRepository->update($input, $id);

        return $this->sendResponse(
            new OrderItemResource($orderItem),
            __('messages.updated', ['model' => __('models/orderItems.singular')])
        );
    }

    /**
     * Remove the specified OrderItem from storage.
     * DELETE /orderItems/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var OrderItem $orderItem */
        $orderItem = $this->orderItemRepository->find($id);

        if (empty($orderItem)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/orderItems.singular')])
            );
        }

        $orderItem->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/orderItems.singular')])
        );
    }
}

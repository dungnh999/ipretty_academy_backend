<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCartAPIRequest;
use App\Http\Requests\API\UpdateCartAPIRequest;
use App\Models\Cart;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CartResource;
use App\Repositories\CartItemRepository;
use Response;

/**
 * Class CartController
 * @package App\Http\Controllers\API
 */

class CartAPIController extends AppBaseController
{
    /** @var  CartRepository */
    private $cartRepository;
    private $cartItemRepository;

    public function __construct(CartRepository $cartRepo, CartItemRepository $cartItemRepository)
    {
        $this->cartRepository = $cartRepo;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * Display a listing of the Cart.
     * GET|HEAD /carts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $carts = $this->cartRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            CartResource::collection($carts),
            __('messages.retrieved', ['model' => __('models/carts.plural')])
        );
    }

    /**
     * Store a newly created Cart in storage.
     * POST /carts
     *
     * @param CreateCartAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCartAPIRequest $request)
    {
        if ($request->cart_id) {
            $cartHadCourse = $this->cartRepository->checkCart($request->cart_id, $request->course_id);
            if ($cartHadCourse) {
                return $this->sendError(
                    __('messages.course_has_added_into_cart')
                );
            }
        }

        $cart = $this->cartRepository->addToCart($request);

        return $this->sendResponse(
            new CartResource($cart),
            __('messages.add_to_cart')
        );
    }

    /**
     * Display the specified Cart.
     * GET|HEAD /carts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Cart $cart */
        $cart = $this->cartRepository->find($id);

        if (empty($cart)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/carts.singular')])
            );
        }

        return $this->sendResponse(
            new CartResource($cart),
            __('messages.retrieved', ['model' => __('models/carts.singular')])
        );      
    }

    /**
     * Update the specified Cart in storage.
     * PUT/PATCH /carts/{id}
     *
     * @param int $id
     * @param UpdateCartAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCartAPIRequest $request)
    {
        $input = $request->all();

        /** @var Cart $cart */
        $cart = $this->cartRepository->find($id);

        if (empty($cart)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/carts.singular')])
            );
        }

        $cart = $this->cartRepository->update($input, $id);

        return $this->sendResponse(
            new CartResource($cart),
            __('messages.updated', ['model' => __('models/carts.singular')])
        );
    }

    /**
     * Remove the specified Cart from storage.
     * DELETE /carts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Cart $cart */
        $cart = $this->cartRepository->find($id);

        if (empty($cart)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/carts.singular')])
            );
        }

        $cart->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/carts.singular')])
        );
    }

    public function deleteItemOutCart ($cart_id, $cart_item_id) {

        $cart = $this->cartRepository->find($cart_id);

        if (empty($cart)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/carts.singular')])
            );
        }

        $cartItem = $this->cartItemRepository->find($cart_item_id);

        if (empty($cartItem)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/carts.fields.cartItem')])
            );
        }

        $cartItem->delete();

        return $this->sendResponse(
            $cart_id,
            __('messages.deleted', ['model' => __('models/carts.fields.cartItem')])
        );

    }
}

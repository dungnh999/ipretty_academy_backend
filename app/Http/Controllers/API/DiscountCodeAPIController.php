<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDiscountCodeAPIRequest;
use App\Http\Requests\API\UpdateDiscountCodeAPIRequest;
use App\Models\DiscountCode;
use App\Repositories\DiscountCodeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\DiscountCodeResource;
use App\Models\Course;
use Carbon\Carbon;
use Response;

/**
 * Class DiscountCodeController
 * @package App\Http\Controllers\API
 */

class DiscountCodeAPIController extends AppBaseController
{
    /** @var  DiscountCodeRepository */
    private $discountCodeRepository;

    public function __construct(DiscountCodeRepository $discountCodeRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->discountCodeRepository = $discountCodeRepo;
    }

    /**
     * Display a listing of the DiscountCode.
     * GET|HEAD /discountCodes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $params = request()->query();

        $discount_code = $this->discountCodeRepository->allDiscountCode($params);

        return $this->sendResponse(
            $discount_code,
            __('messages.retrieved', ['model' => __('models/posts.plural')])
        );
    }

    /**
     * Store a newly created DiscountCode in storage.
     * POST /discountCodes
     *
     * @param CreateDiscountCodeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDiscountCodeAPIRequest $request)
    {
        $user = auth()->user();
        $request->request->add(['created_by' => $user->id]);
        $input = $request->all();

        $discountCode = $this->discountCodeRepository->create($input);

        return $this->sendResponse(
            new DiscountCodeResource($discountCode),
            __('messages.saved', ['model' => __('models/discountCodes.singular')])
        );
    }

    /**
     * Display the specified DiscountCode.
     * GET|HEAD /discountCodes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DiscountCode $discountCode */
        $discountCode = $this->discountCodeRepository->find($id);

        if (empty($discountCode)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/discountCodes.singular')])
            );
        }

        return $this->sendResponse(
            new DiscountCodeResource($discountCode),
            __('messages.retrieved', ['model' => __('models/discountCodes.singular')])
        );
    }

    /**
     * Update the specified DiscountCode in storage.
     * PUT/PATCH /discountCodes/{id}
     *
     * @param int $id
     * @param UpdateDiscountCodeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDiscountCodeAPIRequest $request)
    {
        $input = $request->all();

        /** @var DiscountCode $discountCode */
        $discountCode = $this->discountCodeRepository->find($id);

        $discountCode = $this->discountCodeRepository->update($input, $id);

        return $this->sendResponse(
            new DiscountCodeResource($discountCode),
            __('messages.updated', ['model' => __('models/discountCodes.singular')])
        );
    }

    /**
     * Remove the specified DiscountCode from storage.
     * DELETE /discountCodes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DiscountCode $discountCode */
        $discountCode = $this->discountCodeRepository->find($id);

        if (empty($discountCode)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/discountCodes.singular')])
            );
        }

        $discountCode->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/discountCodes.singular')])
        );
    }

    public function generateDiscountCode()
    {
        $discount_code = $this->discountCodeRepository->generateDiscountCode();
        return $this->sendResponse(
            $discount_code,
            __('messages.generate_discount_code_successfully')
        );
    }

    public function checkDiscountCode(Request $request)
    {

        $user = auth()->user();

        $used_discount_code = $this->discountCodeRepository->checkUsingDiscountCode($user, $request->discount_code);

        if (!$used_discount_code['isExist']) {
            return $this->sendError(
                __('messages.code_is_not_found')
            );
        } else if ($used_discount_code['isUsed']) {
            return $this->sendError(
                __('messages.code_is_used')
            );
        } else if ($used_discount_code['isExpired']) {
            return $this->sendError(
                __('messages.code_is_expired')
            );
        } else if ($used_discount_code['isOutOfStock']) {
            return $this->sendError(
                __('messages.code_is_out_of_stock')
            );
        }

        return $this->sendResponse(
            $used_discount_code['discountCode'],
            __('messages.code_is_valid')
        );
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserDepartmentAPIRequest;
use App\Http\Requests\API\UpdateUserDepartmentAPIRequest;
use App\Models\UserDepartment;
use App\Repositories\UserDepartmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\UserDepartmentResource;
use Response;

/**
 * Class UserDepartmentController
 * @package App\Http\Controllers\API
 */

class UserDepartmentAPIController extends AppBaseController
{
    /** @var  UserDepartmentRepository */
    private $userDepartmentRepository;

    public function __construct(UserDepartmentRepository $userDepartmentRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->userDepartmentRepository = $userDepartmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/userDepartments",
     *      summary="Get a listing of the UserDepartments.",
     *      tags={"UserDepartment"},
     *      description="Get all UserDepartments",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/UserDepartment")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $userDepartments = $this->userDepartmentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            UserDepartmentResource::collection($userDepartments),
            __('messages.retrieved', ['model' => __('models/userDepartments.plural')])
        );
    }

    /**
     * @param CreateUserDepartmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/userDepartments",
     *      summary="Store a newly created UserDepartment in storage",
     *      tags={"UserDepartment"},
     *      description="Store UserDepartment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserDepartment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserDepartment")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/UserDepartment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserDepartmentAPIRequest $request)
    {
        $input = $request->all();

        $userDepartment = $this->userDepartmentRepository->create($input);

        return $this->sendResponse(
            new UserDepartmentResource($userDepartment),
            __('messages.saved', ['model' => __('models/userDepartments.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/userDepartments/{id}",
     *      summary="Display the specified UserDepartment",
     *      tags={"UserDepartment"},
     *      description="Get UserDepartment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserDepartment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/UserDepartment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var UserDepartment $userDepartment */
        $userDepartment = $this->userDepartmentRepository->find($id);

        if (empty($userDepartment)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/userDepartments.singular')])
            );
        }

        return $this->sendResponse(
            new UserDepartmentResource($userDepartment),
            __('messages.retrieved', ['model' => __('models/userDepartments.singular')])
        );
    }

    /**
     * @param int $id
     * @param UpdateUserDepartmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/userDepartments/{id}",
     *      summary="Update the specified UserDepartment in storage",
     *      tags={"UserDepartment"},
     *      description="Update UserDepartment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserDepartment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserDepartment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserDepartment")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/UserDepartment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserDepartmentAPIRequest $request)
    {

        $input = $request->all();

        /** @var UserDepartment $userDepartment */
        $userDepartment = $this->userDepartmentRepository->find($id);

        if (empty($userDepartment)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/userDepartments.singular')])
            );
        }

        $userDepartment = $this->userDepartmentRepository->update($input, $id);

        return $this->sendResponse(
            new UserDepartmentResource($userDepartment),
            __('messages.updated', ['model' => __('models/userDepartments.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/userDepartments/{id}",
     *      summary="Remove the specified UserDepartment from storage",
     *      tags={"UserDepartment"},
     *      description="Delete UserDepartment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserDepartment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var UserDepartment $userDepartment */
        $userDepartment = $this->userDepartmentRepository->find($id);

        if (empty($userDepartment)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/userDepartments.singular')])
            );
        }

        $userDepartment->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/userDepartments.singular')])
        );
    }
}

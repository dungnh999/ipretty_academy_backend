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

    public function store(CreateUserDepartmentAPIRequest $request)
    {
        $input = $request->all();

        $userDepartment = $this->userDepartmentRepository->create($input);

        return $this->sendResponse(
            new UserDepartmentResource($userDepartment),
            __('messages.saved', ['model' => __('models/userDepartments.singular')])
        );
    }

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

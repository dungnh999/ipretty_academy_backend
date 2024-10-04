<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSessionAPIRequest;
use App\Http\Requests\API\UpdateSessionAPIRequest;
use App\Models\Session;
use App\Repositories\SessionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\SessionResource;
use Response;

/**
 * Class SessionController
 * @package App\Http\Controllers\API
 */

class SessionAPIController extends AppBaseController
{
    /** @var  SessionRepository */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepo)
    {
        $this->sessionRepository = $sessionRepo;
    }

    public function index(Request $request)
    {
        $sessions = $this->sessionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            SessionResource::collection($sessions),
            __('messages.retrieved', ['model' => __('models/sessions.plural')])
        );
    }

    public function store(CreateSessionAPIRequest $request)
    {
        $input = $request->all();

        $session = $this->sessionRepository->create($input);

        return $this->sendResponse(
            new SessionResource($session),
            __('messages.saved', ['model' => __('models/sessions.singular')])
        );
    }

    public function show($id)
    {
        /** @var Session $session */
        $session = $this->sessionRepository->find($id);

        if (empty($session)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/sessions.singular')])
            );
        }

        return $this->sendResponse(
            new SessionResource($session),
            __('messages.retrieved', ['model' => __('models/sessions.singular')])
        );
    }

    public function update($id, UpdateSessionAPIRequest $request)
    {
        $input = $request->all();

        /** @var Session $session */
        $session = $this->sessionRepository->find($id);

        if (empty($session)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/sessions.singular')])
            );
        }

        $session = $this->sessionRepository->update($input, $id);

        return $this->sendResponse(
            new SessionResource($session),
            __('messages.updated', ['model' => __('models/sessions.singular')])
        );
    }

    public function destroy($id)
    {
        /** @var Session $session */
        $session = $this->sessionRepository->find($id);

        if (empty($session)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/sessions.singular')])
            );
        }

        $session->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/sessions.singular')])
        );
    }
}

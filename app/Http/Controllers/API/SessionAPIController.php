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

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sessions",
     *      summary="Get a listing of the Sessions.",
     *      tags={"Session"},
     *      description="Get all Sessions",
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
     *                  @SWG\Items(ref="#/definitions/Session")
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

    /**
     * @param CreateSessionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sessions",
     *      summary="Store a newly created Session in storage",
     *      tags={"Session"},
     *      description="Store Session",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Session that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Session")
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
     *                  ref="#/definitions/Session"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSessionAPIRequest $request)
    {
        $input = $request->all();

        $session = $this->sessionRepository->create($input);

        return $this->sendResponse(
            new SessionResource($session),
            __('messages.saved', ['model' => __('models/sessions.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sessions/{id}",
     *      summary="Display the specified Session",
     *      tags={"Session"},
     *      description="Get Session",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Session",
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
     *                  ref="#/definitions/Session"
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

    /**
     * @param int $id
     * @param UpdateSessionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sessions/{id}",
     *      summary="Update the specified Session in storage",
     *      tags={"Session"},
     *      description="Update Session",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Session",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Session that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Session")
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
     *                  ref="#/definitions/Session"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sessions/{id}",
     *      summary="Remove the specified Session from storage",
     *      tags={"Session"},
     *      description="Delete Session",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Session",
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

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSessionLessonAPIRequest;
use App\Http\Requests\API\UpdateSessionLessonAPIRequest;
use App\Models\SessionLesson;
use App\Repositories\SessionLessonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\SessionLessonResource;
use Response;

/**
 * Class SessionLessonController
 * @package App\Http\Controllers\API
 */

class SessionLessonAPIController extends AppBaseController
{
    /** @var  SessionLessonRepository */
    private $sessionLessonRepository;

    public function __construct(SessionLessonRepository $sessionLessonRepo)
    {
        $this->sessionLessonRepository = $sessionLessonRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sessionLessons",
     *      summary="Get a listing of the SessionLessons.",
     *      tags={"SessionLesson"},
     *      description="Get all SessionLessons",
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
     *                  @SWG\Items(ref="#/definitions/SessionLesson")
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
        $sessionLessons = $this->sessionLessonRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            SessionLessonResource::collection($sessionLessons),
            __('messages.retrieved', ['model' => __('models/sessionLessons.plural')])
        );
    }

    /**
     * @param CreateSessionLessonAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sessionLessons",
     *      summary="Store a newly created SessionLesson in storage",
     *      tags={"SessionLesson"},
     *      description="Store SessionLesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SessionLesson that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SessionLesson")
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
     *                  ref="#/definitions/SessionLesson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSessionLessonAPIRequest $request)
    {
        $input = $request->all();

        $sessionLesson = $this->sessionLessonRepository->create($input);

        return $this->sendResponse(
            new SessionLessonResource($sessionLesson),
            __('messages.saved', ['model' => __('models/sessionLessons.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sessionLessons/{id}",
     *      summary="Display the specified SessionLesson",
     *      tags={"SessionLesson"},
     *      description="Get SessionLesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SessionLesson",
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
     *                  ref="#/definitions/SessionLesson"
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
        /** @var SessionLesson $sessionLesson */
        $sessionLesson = $this->sessionLessonRepository->find($id);

        if (empty($sessionLesson)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/sessionLessons.singular')])
            );
        }

        return $this->sendResponse(
            new SessionLessonResource($sessionLesson),
            __('messages.retrieved', ['model' => __('models/sessionLessons.singular')])
        );
    }

    /**
     * @param int $id
     * @param UpdateSessionLessonAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sessionLessons/{id}",
     *      summary="Update the specified SessionLesson in storage",
     *      tags={"SessionLesson"},
     *      description="Update SessionLesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SessionLesson",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SessionLesson that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SessionLesson")
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
     *                  ref="#/definitions/SessionLesson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSessionLessonAPIRequest $request)
    {
        $input = $request->all();

        /** @var SessionLesson $sessionLesson */
        $sessionLesson = $this->sessionLessonRepository->find($id);

        if (empty($sessionLesson)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/sessionLessons.singular')])
            );
        }

        $sessionLesson = $this->sessionLessonRepository->update($input, $id);

        return $this->sendResponse(
            new SessionLessonResource($sessionLesson),
            __('messages.updated', ['model' => __('models/sessionLessons.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sessionLessons/{id}",
     *      summary="Remove the specified SessionLesson from storage",
     *      tags={"SessionLesson"},
     *      description="Delete SessionLesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SessionLesson",
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
        /** @var SessionLesson $sessionLesson */
        $sessionLesson = $this->sessionLessonRepository->find($id);

        if (empty($sessionLesson)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/sessionLessons.singular')])
            );
        }

        $sessionLesson->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/sessionLessons.singular')])
        );
    }
}

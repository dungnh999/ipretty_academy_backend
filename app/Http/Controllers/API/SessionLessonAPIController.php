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

    public function store(CreateSessionLessonAPIRequest $request)
    {
        $input = $request->all();

        $sessionLesson = $this->sessionLessonRepository->create($input);

        return $this->sendResponse(
            new SessionLessonResource($sessionLesson),
            __('messages.saved', ['model' => __('models/sessionLessons.singular')])
        );
    }

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

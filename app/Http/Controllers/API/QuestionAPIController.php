<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuestionAPIRequest;
use App\Http\Requests\API\UpdateQuestionAPIRequest;
use App\Models\Question;
use App\Repositories\QuestionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\QuestionResource;
use App\Repositories\MediaRepository;
use Response;

/**
 * Class QuestionController
 * @package App\Http\Controllers\API
 */

class QuestionAPIController extends AppBaseController
{
    /** @var  QuestionRepository */
    private $questionRepository;
    private $mediaRepository;

    public function __construct(QuestionRepository $questionRepo, MediaRepository $mediaRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->questionRepository = $questionRepo;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Display a listing of the Question.
     * GET|HEAD /questions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $questions = $this->questionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            QuestionResource::collection($questions),
            __('messages.retrieved', ['model' => __('models/questions.plural')])
        );
    }

    /**
     * Store a newly created Question in storage.
     * POST /questions
     *
     * @param CreateQuestionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateQuestionAPIRequest $request)
    {
        $input = $request->all();

        $question = $this->questionRepository->create($input);

        return $this->sendResponse(
            new QuestionResource($question),
            __('messages.saved', ['model' => __('models/questions.singular')])
        );
    }

    /**
     * Display the specified Question.
     * GET|HEAD /questions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Question $question */
        $question = $this->questionRepository->find($id);

        if (empty($question)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questions.singular')])
            );
        }

        return $this->sendResponse(
            new QuestionResource($question),
            __('messages.retrieved', ['model' => __('models/questions.singular')])
        );
    }

    /**
     * Update the specified Question in storage.
     * PUT/PATCH /questions/{id}
     *
     * @param int $id
     * @param UpdateQuestionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuestionAPIRequest $request)
    {
        $input = $request->all();

        /** @var Question $question */
        $question = $this->questionRepository->find($id);

        if (empty($question)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questions.singular')])
            );
        }

        $question = $this->questionRepository->update($input, $id);

        return $this->sendResponse(
            new QuestionResource($question),
            __('messages.updated', ['model' => __('models/questions.singular')])
        );
    }

    /**
     * Remove the specified Question from storage.
     * DELETE /questions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Question $question */
        $question = $this->questionRepository->find($id);

        if (empty($question)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questions.singular')])
            );
        }

        $question->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/questions.singular')])
        );
    }

    public function deleteQuestionAttachment($question_id, Request $request)
    {
        $question = $this->questionRepository->find($question_id);

        if (empty($question)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questions.singular')])
            );
        }

        $media = $this->mediaRepository->findByModelAndId($question_id, $request->media_id);

        if (empty($media)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questions.fields.question_attachments')])
            );
        }

        $deletemedia = $this->questionRepository->destroyMedia($media, $question_id);

        return $this->sendResponse(
            $deletemedia,
            __('messages.deleted', ['model' => __('models/questions.fields.question_attachments')])
        );
    }
}

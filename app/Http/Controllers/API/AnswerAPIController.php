<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAnswerAPIRequest;
use App\Http\Requests\API\UpdateAnswerAPIRequest;
use App\Models\Answer;
use App\Repositories\AnswerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\AnswerResource;
use App\Models\Course;
use App\Models\LearningProcess;
use App\Repositories\CourseStudentRepository;
use App\Repositories\LearningProcessRepository;
use Response;

/**
 * Class AnswerController
 * @package App\Http\Controllers\API
 */

class AnswerAPIController extends AppBaseController
{
    /** @var  AnswerRepository */
    private $answerRepository;
    private $learningProcessRepository;

    public function __construct(AnswerRepository $answerRepo, LearningProcessRepository $learningProcessRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->answerRepository = $answerRepo;
        $this->learningProcessRepository = $learningProcessRepository;
    }

    /**
     * Display a listing of the Answer.
     * GET|HEAD /answers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $answers = $this->answerRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            AnswerResource::collection($answers),
            __('messages.retrieved', ['model' => __('models/answers.plural')])
        );
    }

    /**
     * Store a newly created Answer in storage.
     * POST /answers
     *
     * @param CreateAnswerAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAnswerAPIRequest $request)
    {
        $user = auth()->user();

        $learning_process = $this->learningProcessRepository->findByCondition($request->course_id, $request->survey_id, $user->id);

        // if ($learning_process && $request->isDraft) {
        //     $this->answerRepository->deleteOldAnswers($request->survey_id, $user->id);
        // }

        $input = $request->all();

        $response = $this->answerRepository->create($input);

        if (!$response["success"]) {
            return $this->sendError(
                __('messages.answer_error')
            );
        }

        $answers = $this->answerRepository->getResultDoingSurvey($request->survey_id, $user->id);

        $message = __('messages.saved', ['model' => __('models/answers.singular')]);
        if ($request->isDraft) {
            $message = __('messages.savedDraft', ['model' => __('models/answers.singular')]);
        }

        return $this->sendResponse(
            $answers,
            $message
        );
    }

    /**
     * Display the specified Answer.
     * GET|HEAD /answers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Answer $answer */
        $answer = $this->answerRepository->find($id);

        if (empty($answer)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/answers.singular')])
            );
        }

        return $this->sendResponse(
            new AnswerResource($answer),
            __('messages.retrieved', ['model' => __('models/answers.singular')])
        );
    }

    /**
     * Update the specified Answer in storage.
     * PUT/PATCH /answers/{id}
     *
     * @param int $id
     * @param UpdateAnswerAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAnswerAPIRequest $request)
    {
        $input = $request->all();

        /** @var Answer $answer */
        $answer = $this->answerRepository->find($id);

        if (empty($answer)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/answers.singular')])
            );
        }

        $answer = $this->answerRepository->update($input, $id);

        return $this->sendResponse(
            new AnswerResource($answer),
            __('messages.updated', ['model' => __('models/answers.singular')])
        );
    }

    /**
     * Remove the specified Answer from storage.
     * DELETE /answers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Answer $answer */
        $answer = $this->answerRepository->find($id);

        if (empty($answer)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/answers.singular')])
            );
        }

        $answer->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/answers.singular')])
        );
    }

    public function reWorkSurvey (Request $request) {
        $user = auth()->user();

        $learning_process = $this->learningProcessRepository->findByCourseAndSurvey($request->course_id, $request->survey_id, $user->id);

        if (empty($learning_process)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/learningProcesses.singular')])
            );
        }

        $this->answerRepository->deleteOldAnswers($request->survey_id, $user->id);

        return $this->sendSuccess(
            __('messages.rework_survey')
        );
        
    }
}

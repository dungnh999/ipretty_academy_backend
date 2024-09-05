<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSurveyAPIRequest;
use App\Http\Requests\API\UpdateSurveyAPIRequest;
use App\Models\Survey;
use App\Repositories\SurveyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\SurveyForUserResource;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyShortTermResource;
use Response;

/**
 * Class SurveyController
 * @package App\Http\Controllers\API
 */

class SurveyAPIController extends AppBaseController
{
    /** @var  SurveyRepository */
    private $surveyRepository;

    public function __construct(SurveyRepository $surveyRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->surveyRepository = $surveyRepo;
    }

    /**
     * Display a listing of the Survey.
     * GET|HEAD /surveys
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $surveys = $this->surveyRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            SurveyResource::collection($surveys),
            __('messages.retrieved', ['model' => __('models/surveys.plural')])
        );
    }

    /**
     * Store a newly created Survey in storage.
     * POST /surveys
     *
     * @param CreateSurveyAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSurveyAPIRequest $request)
    {
        $input = $request->all();

        $response = $this->surveyRepository->create($input);

        if (!$response["validJson"]) {
            return $this->sendError(
                __('messages.invalid_format', ['model' => __('models/surveys.fields.questions_data')])
            );
        }

        if (count($response["isRequiredFieldCommon"])) {
            $isRequiredField = $response["isRequiredFieldCommon"];

            foreach ($isRequiredField as $key => $error) {
                $data[$key] = __('messages.is_required', ['model' => __('models/surveys.fields.' . $error)]);
            }

            return $this->sendError(
                __('messages.is_required', ['model' => __('models/surveys.fields.questions_data')]),
                422,
                $data
            );
        }

        if (
            count($response["isRequiredField"]["question_title"]) ||
            count($response["isRequiredField"]["question_type"]) ||
            count($response["isRequiredField"]["option_body"])  ||
            count($response["isRequiredField"]["right_answer"])
        ) {

            $isRequiredField = $response["isRequiredField"];

            foreach ($isRequiredField as $key => $field) {

                if (!count($isRequiredField[$key])) {
                    unset($isRequiredField[$key]);
                }

                if ($key == 'question_title') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.question_title')]);

                }
                else if ($key == 'question_type') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.question_type')]);

                }
                else if ($key == 'option_body') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.option_body')]);

                }
                else if ($key == 'right_answer') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.right_answer')]);

                }
                if (isset($isRequiredField[$key]) && count($isRequiredField[$key])) {
                    $error = new \stdClass;
                    $error->{$isRequiredField[$key][0]} = $message;
                    $isRequiredField[$key] = $error;
                }
            }
            return $this->sendError(
                __('messages.is_required', ['model' => __('models/surveys.fields.questions_data')]),
                422,
                $isRequiredField
            );
        }

        if (
            count($response["isNotFoundField"]["questions"]) ||
            count($response["isNotFoundField"]["options"])
        ) {

            $isNotFoundField = $response["isNotFoundField"];

            foreach ($isNotFoundField as $key => $field) {

                if (!count($isNotFoundField[$key])) {
                    unset($isNotFoundField[$key]);
                }
            }

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/surveys.fields.questions_data')]),
                422,
                $isNotFoundField
            );
        }

        if (count($response["isInvalidFormat"]["question_attachments"]) || count($response["isInvalidFormat"]["option_attachments"])) {

            $isInvalidFormat = $response["isInvalidFormat"];

            $validExtensions = "jpg, jpeg, png, gif";

            return $this->sendError(
                __('messages.invalid_format_image_file', ['values' => $validExtensions]),
                422,
                $isInvalidFormat
            );
        }

        if (count($response["isInvalidSize"]["question_attachments"]) || count($response["isInvalidSize"]["option_attachments"])) {

            $isInvalidSize = $response["isInvalidSize"];

            $validSize = "10 Mb";

            return $this->sendError(
                __('messages.invalid_size_image_file', ['values' => $validSize]),
                422,
                $isInvalidSize
            );
        }

        if (count($response["isInvalidFormat"]["right_answer"])) {

            $isInvalidFormat = $response["isInvalidFormat"];

            $validExtensions = "missing_right_answer";

            return $this->sendError(
                __('messages.missing_right_answer', ['values' => $validExtensions]),
                422,
                $isInvalidFormat
            );
        }
        
        return $this->sendResponse(
            new SurveyResource($response["model"]),
            __('messages.saved', ['model' => __('models/surveys.singular')])
        );
    }

    /**
     * Display the specified Survey.
     * GET|HEAD /surveys/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {

        /** @var Survey $survey */
        $survey = $this->surveyRepository->find($id);

        if (empty($survey)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/surveys.singular')])
            );
        }

        return $this->sendResponse(
            new SurveyResource($survey),
            __('messages.retrieved', ['model' => __('models/surveys.singular')])
        );
    }

    /**
     * Update the specified Survey in storage.
     * PUT/PATCH /surveys/{id}
     *
     * @param int $id
     * @param UpdateSurveyAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSurveyAPIRequest $request)
    {

        $input = $request->all();

        /** @var Survey $survey */
        $survey = $this->surveyRepository->find($id);

        if (empty($survey)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/surveys.singular')])
            );
        }

        $response = $this->surveyRepository->update($input, $id);

        if (!$response["validJson"]) {
            return $this->sendError(
                __('messages.invalid_format', ['model' => __('models/surveys.fields.questions_data')])
            );
        }

        if (count($response["isRequiredFieldCommon"])) {
            $isRequiredField = $response["isRequiredFieldCommon"];

            foreach ($isRequiredField as $key => $error) {
                $data[$key] = __('messages.is_required', ['model' => __('models/surveys.fields.' . $error)]);
            }

            return $this->sendError(
                __('messages.is_required', ['model' => __('models/surveys.fields.questions_data')]),
                422,
                $data
            );
        }

        if (
            count($response["isRequiredField"]["question_title"]) || 
            count($response["isRequiredField"]["question_type"]) || 
            count($response["isRequiredField"]["option_body"]) || 
            count($response["isRequiredField"]["right_answer"])
            ) {

            $isRequiredField = $response["isRequiredField"];

            foreach ($isRequiredField as $key => $field) {

                if (!count($isRequiredField[$key])) {
                    unset($isRequiredField[$key]);
                }
                
                if ($key == 'question_title') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.question_title')]);
                } else if ($key == 'question_type') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.question_type')]);
                } else if ($key == 'option_body') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.option_body')]);
                } else if ($key == 'right_answer') {
                    $message = __('messages.is_required', ['model' => __('models/surveys.fields.right_answer')]);
                }
                if (isset($isRequiredField[$key]) && count($isRequiredField[$key])) {
                    $error = new \stdClass;
                    $error->{$isRequiredField[$key][0]} = $message;
                    $isRequiredField[$key] = $error;
                }
            }

            return $this->sendError(
                __('messages.is_required', ['model' => __('models/surveys.fields.questions_data')]),
                422,
                $isRequiredField
            );
        }

        if (count($response["isInvalidFormat"]["right_answer"])) {

            $isInvalidFormat = $response["isInvalidFormat"];

            $validExtensions = "missing_right_answer";

            return $this->sendError(
                __('messages.missing_right_answer', ['values' => $validExtensions]),
                422,
                $isInvalidFormat
            );
        }

        if (count($response["isInvalidFormat"]["question_attachments"]) || count($response["isInvalidFormat"]["option_attachments"])) {

            $isInvalidFormat = $response["isInvalidFormat"];

            $validExtensions = "jpg, jpeg, png, gif";

            return $this->sendError(
                __('messages.invalid_format_image_file', ['values' => $validExtensions]),
                422,
                $isInvalidFormat
            );
        }

        if (count($response["isInvalidSize"]["question_attachments"]) || count($response["isInvalidSize"]["option_attachments"])) {

            $isInvalidSize = $response["isInvalidSize"];

            $validSize = "10 Mb";

            return $this->sendError(
                __('messages.invalid_size_image_file', ['values' => $validSize]),
                422,
                $isInvalidSize
            );
        }

        return $this->sendResponse(
            new SurveyResource($response["model"]),
            __('messages.updated', ['model' => __('models/surveys.singular')])
        );
    }

    /**
     * Remove the specified Survey from storage.
     * DELETE /surveys/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {

        /** @var Survey $survey */
        $survey = $this->surveyRepository->find($id);

        if (empty($survey)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/surveys.singular')])
            );
        }

        $survey->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/surveys.singular')])
        );
    }

    /**
     * Display the specified Survey.
     * GET|HEAD /surveys/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function surveyDetailForUser($id) 
    {

        /** @var Survey $survey */
        $survey = $this->surveyRepository->find($id);

        if (empty($survey)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/surveys.singular')])
            );
        }

        return $this->sendResponse(
            new SurveyForUserResource($survey),
            __('messages.retrieved', ['model' => __('models/surveys.singular')])
        );
    }

    public function surveyResultDetailForUser(Request $request)
    {

        /** @var Survey $survey */
        $survey = $this->surveyRepository->find($request->survey_id);

        if (empty($survey)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/surveys.singular')])
            );
        }

        $course = $survey->chapter->course;

        if ($course) {
            $courseStudent = $course->studentResultCourse($course->course_id, $request->user_id);
            if (!$courseStudent) {
                return $this->sendError(
                    __('messages.not_found', ['model' => __('models/users.fields.result_for_student')])
                );
            }
        }

        $survey->user_id = $request->user_id;
        
        return $this->sendResponse(
            new SurveyForUserResource($survey),
            __('messages.retrieved', ['model' => __('models/users.fields.result_for_student')])
        );
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuestionOptionAPIRequest;
use App\Http\Requests\API\UpdateQuestionOptionAPIRequest;
use App\Models\QuestionOption;
use App\Repositories\QuestionOptionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\QuestionOptionResource;
use Response;

/**
 * Class QuestionOptionController
 * @package App\Http\Controllers\API
 */

class QuestionOptionAPIController extends AppBaseController
{
    /** @var  QuestionOptionRepository */
    private $questionOptionRepository;

    public function __construct(QuestionOptionRepository $questionOptionRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->questionOptionRepository = $questionOptionRepo;
    }

    /**
     * Display a listing of the QuestionOption.
     * GET|HEAD /questionOptions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $questionOptions = $this->questionOptionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            QuestionOptionResource::collection($questionOptions),
            __('messages.retrieved', ['model' => __('models/questionOptions.plural')])
        );
    }

    /**
     * Store a newly created QuestionOption in storage.
     * POST /questionOptions
     *
     * @param CreateQuestionOptionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateQuestionOptionAPIRequest $request)
    {
        $input = $request->all();

        $questionOption = $this->questionOptionRepository->create($input);

        return $this->sendResponse(
            new QuestionOptionResource($questionOption),
            __('messages.saved', ['model' => __('models/questionOptions.singular')])
        );
    }

    /**
     * Display the specified QuestionOption.
     * GET|HEAD /questionOptions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var QuestionOption $questionOption */
        $questionOption = $this->questionOptionRepository->find($id);

        if (empty($questionOption)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questionOptions.singular')])
            );
        }

        return $this->sendResponse(
            new QuestionOptionResource($questionOption),
            __('messages.retrieved', ['model' => __('models/questionOptions.singular')])
        );
    }

    /**
     * Update the specified QuestionOption in storage.
     * PUT/PATCH /questionOptions/{id}
     *
     * @param int $id
     * @param UpdateQuestionOptionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuestionOptionAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuestionOption $questionOption */
        $questionOption = $this->questionOptionRepository->find($id);

        if (empty($questionOption)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questionOptions.singular')])
            );
        }

        $questionOption = $this->questionOptionRepository->update($input, $id);

        return $this->sendResponse(
            new QuestionOptionResource($questionOption),
            __('messages.updated', ['model' => __('models/questionOptions.singular')])
        );
    }

    /**
     * Remove the specified QuestionOption from storage.
     * DELETE /questionOptions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var QuestionOption $questionOption */
        $questionOption = $this->questionOptionRepository->find($id);

        if (empty($questionOption)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/questionOptions.singular')])
            );
        }

        $questionOption->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/questionOptions.singular')])
        );
    }

    public function deleteOptionAttachment($option_id, Request $request)
    {
        $option = $this->questionOptionRepository->find($option_id);

        if (empty($option)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/options.singular')])
            );
        }

        $media = $this->mediaRepository->findByModelAndId($option_id, $request->media_id);

        if (empty($media)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/options.fields.option_attachments')])
            );
        }

        $deletemedia = $this->questionOptionRepository->destroyMedia($media, $option_id);

        return $this->sendResponse(
            $deletemedia,
            __('messages.deleted', ['model' => __('models/options.fields.option_attachments')])
        );
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFrequentlyAskedQuestionsAPIRequest;
use App\Http\Requests\API\UpdateFrequentlyAskedQuestionsAPIRequest;
use App\Models\FrequentlyAskedQuestions;
use App\Repositories\FrequentlyAskedQuestionsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\FrequentlyAskedQuestionsResource;
use App\Models\CommentFAQ;
use App\Models\FAQCategory;
use App\Models\FAQLike;
use App\Models\FAQQuestion;
use Response;

/**
 * Class FrequentlyAskedQuestionsController
 * @package App\Http\Controllers\API
 */

class FrequentlyAskedQuestionsAPIController extends AppBaseController
{
    /** @var  FrequentlyAskedQuestionsRepository */
    private $frequentlyAskedQuestionsRepository;

    public function __construct(FrequentlyAskedQuestionsRepository $frequentlyAskedQuestionsRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->frequentlyAskedQuestionsRepository = $frequentlyAskedQuestionsRepo;
    }

    /**
     * Display a listing of the FrequentlyAskedQuestions.
     * GET|HEAD /frequentlyAskedQuestions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $params = request()->query();

        $faqs = $this->frequentlyAskedQuestionsRepository->allFaqs($params);

        return $this->sendResponse(
            $faqs,
            __('messages.retrieved', ['model' => __('models/frequentlyAskedQuestions.plural')])
        );

    }

    /**
     * Store a newly created FrequentlyAskedQuestions in storage.
     * POST /frequentlyAskedQuestions
     *
     * @param CreateFrequentlyAskedQuestionsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFrequentlyAskedQuestionsAPIRequest $request)
    {
        $user = auth()->user();
        $request->request->add(['created_by' => $user->id]);
        $input = $request->all();
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->create($input);

        return $this->sendResponse(
            new FrequentlyAskedQuestionsResource($frequentlyAskedQuestions),
            __('messages.saved', ['model' => __('models/frequentlyAskedQuestions.singular')])
        );
    }

    public function createFaqs(CreateFrequentlyAskedQuestionsAPIRequest $request) {

        $input = $request->all();
        // dd($input);
        $response = $this->frequentlyAskedQuestionsRepository->createFaqs($input);

        if (
            count($response["isRequiredField"]["question_name"]) ||
            count($response["isRequiredField"]["answer_name"]) 
        ) {
            $isRequiredField = $response["isRequiredField"];

            foreach ($isRequiredField as $key => $field) {

                if (!count($isRequiredField[$key])) {
                    unset($isRequiredField[$key]);
                }

                if ($key == 'question_name') {
                    $message = __('messages.is_required', ['model' => __('models/frequentlyAskedQuestions.fields.question_name')]);

                }
                else if ($key == 'answer_name') {
                    $message = __('messages.is_required', ['model' => __('models/frequentlyAskedQuestions.fields.answer_name')]);

                }
                if (isset($isRequiredField[$key]) && count($isRequiredField[$key])) {
                    $error = new \stdClass;
                    $error->{$isRequiredField[$key][0]} = $message;
                    $isRequiredField[$key] = $error;
                }
            }
            return $this->sendError(
                __('messages.is_required', ['model' => __('models/frequentlyAskedQuestions.fields.questions_data')]),
                422,
                $isRequiredField
            ); 
        }

        if ($response["model"]) {
            return $this->sendResponse(
                new FrequentlyAskedQuestionsResource($response["model"]),
                __('messages.created', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }  
    }

    public function updateFaqs($id, UpdateFrequentlyAskedQuestionsAPIRequest $request) {

        $input = $request->all();

        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($id);

        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }

        $response = $this->frequentlyAskedQuestionsRepository->updateFaqs($input, $id);

        if (
            count($response["isRequiredField"]["question_name"]) ||
            count($response["isRequiredField"]["answer_name"]) 
        ) {
            $isRequiredField = $response["isRequiredField"];

            foreach ($isRequiredField as $key => $field) {

                if (!count($isRequiredField[$key])) {
                    unset($isRequiredField[$key]);
                }

                if ($key == 'question_name') {
                    $message = __('messages.is_required', ['model' => __('models/frequentlyAskedQuestions.fields.question_name')]);

                }
                else if ($key == 'answer_name') {
                    $message = __('messages.is_required', ['model' => __('models/frequentlyAskedQuestions.fields.answer_name')]);

                }
                if (isset($isRequiredField[$key]) && count($isRequiredField[$key])) {
                    $error = new \stdClass;
                    $error->{$isRequiredField[$key][0]} = $message;
                    $isRequiredField[$key] = $error;
                }
            }
            return $this->sendError(
                __('messages.is_required', ['model' => __('models/frequentlyAskedQuestions.fields.questions_data')]),
                422,
                $isRequiredField
            ); 
        }

        if ($input['delete_question']) {

            $delete_questions = explode(',', $input['delete_question']);

            if (count($delete_questions)) {

                foreach ($delete_questions as $delete_question_id) {
        
                    $faq_question = FAQQuestion::find($delete_question_id);

                    if ($faq_question) {

                        $faq_question->delete();

                    }
                    
                }

            }

        }

        return $this->sendResponse(
            new FrequentlyAskedQuestionsResource($response["model"], $id),
            __('messages.updated', ['model' => __('models/frequentlyAskedQuestions.singular')])
        );
    }

    public function showFaq($id)
    {
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($id);

        // dd($frequentlyAskedQuestions);

        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }

        return $this->sendResponse(
            new FrequentlyAskedQuestionsResource($frequentlyAskedQuestions),
            __('messages.retrieved', ['model' => __('models/frequentlyAskedQuestions.singular')])
        );
    }

    /**
     * Display the specified FrequentlyAskedQuestions.
     * GET|HEAD /frequentlyAskedQuestions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var FrequentlyAskedQuestions $frequentlyAskedQuestions */
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($id);

        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }
        $countLike = $this->frequentlyAskedQuestionsRepository->countLikeAndDislikeFAQ($frequentlyAskedQuestions->id);
        $frequentlyAskedQuestions['count_like'] = $countLike['count_like'];
        $frequentlyAskedQuestions['count_dislike'] = $countLike['count_dislike'];
        $listComments = $this->frequentlyAskedQuestionsRepository->getListCommentFAQ($id);
        $frequentlyAskedQuestions['listComments'] = $listComments;
        return $this->sendResponse(
            new FrequentlyAskedQuestionsResource($frequentlyAskedQuestions),
            __('messages.retrieved', ['model' => __('models/frequentlyAskedQuestions.singular')])
        );
    }

    /**
     * Update the specified FrequentlyAskedQuestions in storage.
     * PUT/PATCH /frequentlyAskedQuestions/{id}
     *
     * @param int $id
     * @param UpdateFrequentlyAskedQuestionsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFrequentlyAskedQuestionsAPIRequest $request)
    {
        $input = $request->all();

        /** @var FrequentlyAskedQuestions $frequentlyAskedQuestions */
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($id);

        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }

        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->update($input, $id);

        return $this->sendResponse(
            new FrequentlyAskedQuestionsResource($frequentlyAskedQuestions),
            __('messages.updated', ['model' => __('models/frequentlyAskedQuestions.singular')])
        );
    }

    /**
     * Remove the specified FrequentlyAskedQuestions from storage.
     * DELETE /frequentlyAskedQuestions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var FrequentlyAskedQuestions $frequentlyAskedQuestions */
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($id);

        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }

        $frequentlyAskedQuestions->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/frequentlyAskedQuestions.singular')])
        );
    }

    public function likeFaq(Request $request,$id)
    {
        $user = auth()->user();
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($id);
        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }

        $request->validate([
            'status' => 'required',
        ]);

        $mess = $this->frequentlyAskedQuestionsRepository->likeAndDislikeFAQ($id,$user->id,$request->status);
        
        return $this->sendSuccess($mess);
    }

    public function getAllListFAQ()
    {
        $getListFAQs = $this->frequentlyAskedQuestionsRepository->getAllListFAQ();
        return $this->sendResponse(
            $getListFAQs,
            __('messages.get_list_faq_successfully')
        );
    }

    public function commentFAQ(Request $request,$faq_id)
    {
        $user = auth()->user();
        $data = $request->all();
        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->find($faq_id);
        if (empty($frequentlyAskedQuestions)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/frequentlyAskedQuestions.singular')])
            );
        }
        if(isset($request->parent_id)){
            $check_parent_id = CommentFAQ::where('id', $request->parent_id)->where('faq_id', $faq_id)->first();
            if(!$check_parent_id){
                return $this->sendError(
                    __('messages.comment_not_found')
                );
            }
        }
        $response = $this->frequentlyAskedQuestionsRepository->commentFAQ($faq_id,$data,$user);
        return $this->sendResponse(
            $response,
            __('messages.comment_successfully')
        );
    }

    public function getQuestionById($faq_id, $question_id) {

        $user = auth()->user();

        $frequentlyAskedQuestions = $this->frequentlyAskedQuestionsRepository->getQuestionById($faq_id, $question_id);

        return $this->sendResponse(
            $frequentlyAskedQuestions,
            __('messages.get_question_successfully')
        );

    }

}

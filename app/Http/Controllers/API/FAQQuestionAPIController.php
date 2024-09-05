<?php

namespace App\Http\Controllers\API;

use App\Contract\CommonBusiness;
use App\Http\Requests\API\CreateFAQQuestionAPIRequest;
use App\Http\Requests\API\UpdateFAQQuestionAPIRequest;
use App\Models\FAQQuestion;
use App\Repositories\FAQQuestionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateCommentAPIRequest;
use App\Http\Resources\FAQQuestionResource;
use App\Jobs\PushNotificationWhenNewCommentFAQ;
use App\Models\User;
use App\Repositories\CommentFAQRepository;
use App\Repositories\FAQLikeRepository;
use Response;

/**
 * Class FAQQuestionController
 * @package App\Http\Controllers\API
 */

class FAQQuestionAPIController extends AppBaseController
{

    use CommonBusiness;
    
    /** @var  FAQQuestionRepository */
    private $fAQQuestionRepository;
    private $fAQLikeRepository;
    private $commentFAQRepository;

    public function __construct(FAQQuestionRepository $fAQQuestionRepo, FAQLikeRepository $fAQLikeRepository, CommentFAQRepository $commentFAQRepository)
    {

        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);

        });
        

        $this->fAQQuestionRepository = $fAQQuestionRepo;
        $this->fAQLikeRepository = $fAQLikeRepository;
        $this->commentFAQRepository = $commentFAQRepository;
    }

    /**
     * Display a listing of the FAQQuestion.
     * GET|HEAD /fAQQuestions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $fAQQuestions = $this->fAQQuestionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            FAQQuestionResource::collection($fAQQuestions),
            __('messages.retrieved', ['model' => __('models/fAQQuestions.plural')])
        );
    }

    /**
     * Store a newly created FAQQuestion in storage.
     * POST /fAQQuestions
     *
     * @param CreateFAQQuestionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFAQQuestionAPIRequest $request)
    {
        $input = $request->all();

        $fAQQuestion = $this->fAQQuestionRepository->create($input);

        return $this->sendResponse(
            new FAQQuestionResource($fAQQuestion),
            __('messages.saved', ['model' => __('models/fAQQuestions.singular')])
        );
    }

    /**
     * Display the specified FAQQuestion.
     * GET|HEAD /fAQQuestions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var FAQQuestion $fAQQuestion */
        $fAQQuestion = $this->fAQQuestionRepository->find($id);

        if (empty($fAQQuestion)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/fAQQuestions.singular')])
            );
        }

        return $this->sendResponse(
            new FAQQuestionResource($fAQQuestion),
            __('messages.retrieved', ['model' => __('models/fAQQuestions.singular')])
        );
    }

    /**
     * Update the specified FAQQuestion in storage.
     * PUT/PATCH /fAQQuestions/{id}
     *
     * @param int $id
     * @param UpdateFAQQuestionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFAQQuestionAPIRequest $request)
    {
        $input = $request->all();

        /** @var FAQQuestion $fAQQuestion */
        $fAQQuestion = $this->fAQQuestionRepository->find($id);

        if (empty($fAQQuestion)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/fAQQuestions.singular')])
            );
        }

        $fAQQuestion = $this->fAQQuestionRepository->update($input, $id);

        return $this->sendResponse(
            new FAQQuestionResource($fAQQuestion),
            __('messages.updated', ['model' => __('models/fAQQuestions.singular')])
        );
    }

    /**
     * Remove the specified FAQQuestion from storage.
     * DELETE /fAQQuestions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var FAQQuestion $fAQQuestion */
        $fAQQuestion = $this->fAQQuestionRepository->find($id);

        if (empty($fAQQuestion)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/fAQQuestions.singular')])
            );
        }

        $fAQQuestion->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/fAQQuestions.singular')])
        );
    }


    public function likeOrDislikeFaqQuestion(Request $request)
    {
        $user = auth()->user();

        $fAQQuestion = $this->fAQQuestionRepository->find($request->question_id);

        if (empty($fAQQuestion)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/fAQQuestions.singular')])
            );
        }

        $input = $request->all();

        $input['user_id'] = $user->id;

        $checkLiked = $this->fAQLikeRepository->checkLiked($input);

        $message = 'messages.like_successfully';

        if ($input['status'] == 'Dislike') {
            $message = 'messages.dislike_successfully';
        }

        if ($checkLiked) {
            $currentStatus = $checkLiked->status;
            if ($currentStatus == $input['status']) {
                $error_message = 'messages.liked';

                if ($input['status'] == 'Dislike') {
                    $error_message = 'messages.disliked';
                }

                return $this->sendError(
                    __($error_message)
                );
            }else {
                $changeLikeOrDislike = $this->fAQLikeRepository->changeLikeOrDislikeFAQQuestion($checkLiked, $input);

                if ($changeLikeOrDislike) {
                    return $this->sendResponse(
                        new FAQQuestionResource($fAQQuestion),
                        __($message)
                    );
                }
            }
        }else {
            $fAQLikeRepository = $this->fAQLikeRepository->likeOrDislikeFAQQuestion($input);
            return $this->sendResponse(
                new FAQQuestionResource($fAQQuestion),
                __($message)
            );
        }

    }


    public function commentQuestion (CreateCommentAPIRequest $request)
    {
        $user = auth()->user();

        $fAQQuestion = $this->fAQQuestionRepository->find($request->question_id);

        if (empty($fAQQuestion)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/fAQQuestions.singular')])
            );
        }

        $input = $request->all();

        $input['commentator_id'] = $user->id;
        $input['comment_type'] = "Text";
        $input['parent_id'] = isset($input['parent_id']) ? $input['parent_id'] : NULL;

        $comments = $this->commentFAQRepository->commentQuestion($input);

        // if ($user->hasRole(['user']) && !$user->hasRole(['admin']) ) {
        $job = (new PushNotificationWhenNewCommentFAQ($comments));
        
        dispatch($job);
        // }

        // if ($user->hasRole(['user']) && !$user->hasRole(['admin']) ) {
        //     $job = (new PushNotificationWhenNewCommentFAQ($comments));
        //     dispatch($job);
        // }
        // $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->get();
        // if(count($admins) > 0 ){
        //     foreach($admins as $admin) {
        //         $member_admin = User::where('id', $admin->id)->first();
                
        //         if($member_admin)
        //         { 
        //             // dd($member_admin->id);
        //             event(new \App\Events\PushNotification($member_admin->id));
        //         }
        //     }
        // }

        $message = 'messages.comment_successfully';
        return $this->sendResponse(
            new FAQQuestionResource($fAQQuestion),
            __($message)
        );
    }
}

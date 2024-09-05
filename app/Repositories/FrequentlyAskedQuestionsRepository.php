<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Http\Resources\FAQQuestionResource;
use App\Models\CommentFAQ;
use App\Models\FAQCategory;
use App\Models\FAQLike;
use App\Models\FAQQuestion;
use App\Models\FrequentlyAskedQuestions;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class FrequentlyAskedQuestionsRepository
 * @package App\Repositories
 * @version October 11, 2021, 10:18 am +07
*/

class FrequentlyAskedQuestionsRepository extends BaseRepository
{
    use CommonBusiness;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        // 'attachments'
    ];

    protected $relations = ['createdBy'];

    protected $relationSearchable = [
        'name'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FrequentlyAskedQuestions::class;
    }

    public function destroyImageComment($media, $comment_id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($comment_id);

        $mediaUrl = $media->getUrl();

        $image_comment = explode(',', $model->bannerUrl);

        $image_comment = array_diff_key($image_comment, [$mediaUrl]);

        $model->bannerUrl = implode(',', $image_comment);

        $model->save();
        
        $media->delete();

        return $model;
    }

    public function countLikeAndDislikeFAQ($faq_id){
        $countFaqLike = FAQLike::where('faq_id', $faq_id)->where('status', 'Like')->count();
        $countFaqDislike = FAQLike::where('faq_id', $faq_id)->where('status', 'Dislike')->count();

        return $data = [
            'count_like' => $countFaqLike,
            'count_dislike' => $countFaqDislike
        ];
    }

    public function likeAndDislikeFAQ($faq_id,$user_id,$status)
    {
        $checkuserLikeFAQ = FAQLike::where('faq_id', $faq_id)->where('user_id', $user_id)->where('status', $status)->first();
        if($checkuserLikeFAQ){
            $checkuserLikeFAQ->delete();
            if($status == 'Like'){
                $mess =  __('messages.unlike_faq_successfully');
            }else {
                $mess =  __('messages.remove_dislike_faq_successfully');
            }
        }else {
            $likeFAQ = new FAQLike();
            $likeFAQ->faq_id = $faq_id;
            $likeFAQ->user_id = $user_id;
            $likeFAQ->status = $status;
            $likeFAQ->save();
            $mess =  __('messages.like_faq_successfully');
            if($status == 'Like'){
                $mess =  __('messages.like_faq_successfully');
            }else {
                $mess =  __('messages.dislike_faq_successfully');
            }
        }
        return $mess;
    }

    public function getAllListFAQ()
    {
        $getListFAQs = FAQCategory::with('frequently_asked_questions')->get();

        if(count($getListFAQs) > 0){
            foreach($getListFAQs as $faq){
                $data = $faq->frequently_asked_questions;
                if(count($data) > 0){
                    foreach($data as $line){
                        $countFAQ = $this->countLikeAndDislikeFAQ($line->id);
                        $line['sum_like_faq'] = $countFAQ['count_like'];
                    }
                }
            }
        }
        return $getListFAQs;
    }

    public function commentFAQ($faq_id,$dataRes,$user)
    {
        $comment = new CommentFAQ();
        $comment->commentator_id = $user->id;
        $comment->faq_id = $faq_id;
        $comment->comment_type = "Text";
        $comment->comment = $dataRes['comment'];
        $comment->parent_id = isset($dataRes['parent_id']) ? $dataRes['parent_id'] : null;
        $comment->save();
        return $comment;
    }

    public function getListCommentFAQ($faq_id)
    {
        $listComments = CommentFAQ::where('faq_id', $faq_id)
                                ->with('comments')->with('comments.created_by')
                                ->with('created_by')
                                ->whereNull('parent_id')
                                ->orderBy('created_at', 'desc')
                                ->get();
        return $listComments;
    }

    public function allFaqs ($params) {

        $query = $this->model->newQuery()->with('createdBy', function($q) {
            $q->select('name', 'id', 'email', 'avatar');
        })->with('faqQuestions');

        // ->with('faqQuestions.likes');
        if (isset($params["isPublished"])) {
            $query = $query->where('isPublished', $params["isPublished"]);
        }
            $query = $query->orderBy('created_at', 'desc');

        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $query = $query->whereIn('isPublished', $status);
        }

        if (isset($params['created_at']) && $params['created_at'] != null) {
            $created_at = $params['created_at'];
            $query = $query->whereDate('created_at', '>=', $created_at);
        }

        if (isset($params['updated_at']) && $params['updated_at'] != null) {
            $updated_at = $params['updated_at'];
            $query = $query->whereDate('updated_at', '<=', $updated_at);
        }

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
        }

        if (isset($params['paging']) && $params['paging']) {
            if (isset($params['perpage']) && $params['perpage'] != null) {

                $perpage = $params['perpage'];

                $model = $query->paginate($perpage);
            } else {
                $model = $query->paginate(PERPAGE);
            }
        } else {
            $model = $query->get();
        }

        return $model;
    }

    public function createFaqs ($input, $request = null) {

        DB::beginTransaction();

        $is_published = false;
        
        if (isset($input["is_published"]) && $input["is_published"]) {
            $is_published = $input["is_published"];
        }

        $user = auth()->user();

        $input["created_by"] = $user->id;

        $model = $this->model->newInstance($input);

        // dd($input);

        $model->save();

        $model->updated_at = NULL;

        $model->save();

        $faqs_resources = $input['faqs_resources'];

        $responses = $this->checkValidFaqQuestionResources($faqs_resources, $is_published);

        if (
            !$responses["validJson"] ||
            count($responses["isRequiredFieldCommon"]) ||
            count($responses["isRequiredField"]["question_name"]) ||
            count($responses["isRequiredField"]["answer_name"])
        ) {
            DB::rollBack();
            return $responses;
        } else {
            $this->insertOrUpdateFaqQuestion ($faqs_resources, $model);
        }

        DB::commit();

        $responses["model"] = $model;

        return  $responses;
    }

    public function updateFaqs($input, $id)
    {
        DB::beginTransaction();

        $is_published = false;

        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->updated_at = date(Carbon::now());

        $model->save();

        $faqs_resources = $input['faqs_resources'];

        // dd($faqs_resources);

        $responses = $this->checkValidFaqQuestionResources($faqs_resources, $is_published);

        // dd($responses);

        if (
            !$responses["validJson"] ||
            count($responses["isRequiredFieldCommon"]) ||
            count($responses["isRequiredField"]["question_name"]) ||
            count($responses["isRequiredField"]["answer_name"])
        ) {
            DB::rollBack();
            return $responses;
        } else {
            $this->insertOrUpdateFaqQuestion ($faqs_resources, $model);
        }

        DB::commit();

        $responses["model"] = $model;

        return  $responses;
    }

    public function insertOrUpdateFaqQuestion ($faqs_resources, $model) {
        
        $currentFaq = $model->toArray();
        // dd($model);
        $questions = $faqs_resources["questions"];
        
        foreach ($questions as $key => $question) {
            // dd($question);
            if (isset($question["question_id"]) && $question["question_id"] != null) {
                $newQuestion = FAQQuestion::find($question["question_id"]);
                $newQuestion->question_name = $question["question_name"];
                $newQuestion->answer_name = $question["answer_name"];
                $newQuestion->number_order = $question["number_order"];
                $newQuestion->save();
            } else {
                $newQuestion = FAQQuestion::create(
                    [
                        "question_name" => $question["question_name"],
                        "answer_name" => $question["answer_name"],
                        "number_order" => $question["number_order"],
                        "faq_id" => $currentFaq['id'],
                    ]
                );
            }
        }
    }

    public function checkValidFaqQuestionResources ($faqs_resources, $is_published = false) {

        $isInValidField = [

            "validJson" => true,

            "isRequiredFieldCommon" => [],

            "isRequiredField" => [
                "question_name" => [],
                "answer_name" => [],
            ],

            "isNotFoundField" => [
                "questions" => [],
            ],

            "model" => null
        ];

        // dd($faqs_resources["questions"]);

        if ($faqs_resources) {
            // dd($faqs_resources["questions"]);
            if (!$faqs_resources["questions"]) {
                array_push($isInValidField["isRequiredFieldCommon"], 'questions');
                return $isInValidField;
            }

            if (empty($faqs_resources["questions"]) || (!empty($faqs_resources["questions"]) && !count($faqs_resources["questions"]))) {
                if (!in_array('questions', $isInValidField["isRequiredFieldCommon"])) {
                    array_push($isInValidField["isRequiredFieldCommon"], 'questions');
                }
            }else {
                $questions = $faqs_resources["questions"];
                // dd($questions);
                foreach ($questions as $keyq => $question) 
                {

                    if (empty($question["question_name"])) {
                        if (
                            (array_key_exists("question_name", $isInValidField["isRequiredField"]) &&
                                $isInValidField["isRequiredField"]["question_name"] != $question["number_order"]) ||
                            !array_key_exists("question_name", $isInValidField["isRequiredField"])
                        ) 
                        {
                            array_push($isInValidField["isRequiredField"]["question_name"], $question["number_order"]);
                        }
                    }

                    if (empty($question["answer_name"])) {
                        if (
                            (array_key_exists("answer_name", $isInValidField["isRequiredField"]) &&
                                $isInValidField["isRequiredField"]["answer_name"] != $question["number_order"]) ||
                            !array_key_exists("answer_name", $isInValidField["isRequiredField"])
                        ) 
                        {
                            array_push($isInValidField["isRequiredField"]["answer_name"], $question["number_order"]);
                        }
                    }
                }
            }

            return $isInValidField;
        }
    }

    public function getQuestionById($faq_id, $question_id) {

        $questions = FAQQuestion::where('faq_id', $faq_id)->where('question_id', $question_id)->get();

        $new_questions = FAQQuestionResource::collection($questions);

        // dd($questions);

        return $new_questions;
        
    }
}

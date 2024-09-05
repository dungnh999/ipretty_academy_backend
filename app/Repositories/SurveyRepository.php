<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Survey;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class SurveyRepository
 * @package App\Repositories
 * @version September 17, 2021, 7:46 am UTC
*/

class SurveyRepository extends BaseRepository
{

    use CommonBusiness;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'survey_title',
        'created_by',
        // 'survey_duration',
        'percent_to_pass',
        'question_per_page'
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
        return Survey::class;
    }

    public function create($input) {

        DB::beginTransaction();

        $percent_to_pass = $input["percent_to_pass"];

        $model = $this->model->newInstance($input);

        $model->save();

        $questions_data = $input['questions_data'];

        $responses = $this->checkValidQuestions($questions_data);
        // var_dump($responses["isInvalidFormat"]);
        // return(3);
        if (
            !$responses["validJson"] ||
            count($responses["isRequiredFieldCommon"]) ||
            count($responses["isRequiredField"]["question_title"]) ||
            count($responses["isRequiredField"]["question_type"]) ||
            count($responses["isRequiredField"]["option_body"]) ||
            count($responses["isRequiredField"]["right_answer"]) ||
            count($responses["isInvalidFormat"]["right_answer"]) ||
            count($responses["isInvalidFormat"]["question_attachments"]) ||
            count($responses["isInvalidFormat"]["option_attachments"]) ||
            count($responses["isInvalidSize"]["question_attachments"]) ||
            count($responses["isInvalidSize"]["option_attachments"])
        ) {
            DB::rollBack();
            return $responses;
        } else {

            $this->insertOrUpdateQuestion ($questions_data, $model);
        }

        DB::commit();
        $responses["model"] = $model;

        return  $responses;
    }

    public function update ($input, $id) {

        DB::beginTransaction();

        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        $questions_data = $input['questions_data'];

        $responses = $this->checkValidQuestions($questions_data);   
  
        if (
            !$responses["validJson"] ||
            count($responses["isRequiredFieldCommon"]) ||
            count($responses["isRequiredField"]["question_title"]) ||
            count($responses["isRequiredField"]["question_type"]) ||
            count($responses["isRequiredField"]["option_body"]) ||
            count($responses["isRequiredField"]["right_answer"]) ||
            count($responses["isNotFoundField"]["questions"]) ||
            count($responses["isNotFoundField"]["options"]) ||
            count($responses["isInvalidFormat"]["right_answer"]) ||
            count($responses["isInvalidFormat"]["question_attachments"]) ||
            count($responses["isInvalidFormat"]["option_attachments"]) ||
            count($responses["isInvalidSize"]["question_attachments"]) ||
            count($responses["isInvalidSize"]["option_attachments"])
        ) {
            DB::rollBack();
            return $responses;
        } else {
            $this->insertOrUpdateQuestion($questions_data, $model);
        }

        DB::commit();

        $responses["model"] = $model;

        return  $responses;

    }

    public function checkValidQuestions ($questions_data) {

        $isInValidField = [

            "validJson" => true,

            "isRequiredFieldCommon" => [],

            "isRequiredField" => [
                "question_title" => [],
                "question_type" => [],
                "option_body" => [],
                "right_answer" => [],
            ],

            "isNotFoundField" => [
                "questions" => [],
                "options" => [],
            ],

            "isInvalidFormat" => [
                "right_answer" => [],
                "question_attachments" => [],
                "option_attachments" => [],
            ],

            "isInvalidSize" => [
                "question_attachments" => [],
                "option_attachments" => [],
            ],

            "model" => null
        ];

        if ($questions_data) {
            if (!$questions_data["questions"]) {
                array_push($isInValidField["isRequiredFieldCommon"], 'questions');
                // $isInValidField["validJson"] = false;
                return $isInValidField;
            }

            if (empty($questions_data["questions"]) || (!empty($questions_data["questions"]) && !count($questions_data["questions"]))) {
                if (!in_array('questions', $isInValidField["isRequiredFieldCommon"])) {
                    array_push($isInValidField["isRequiredFieldCommon"], 'questions');
                }
            }else {
                $survey_questions = $questions_data["questions"];

                foreach ($survey_questions as $keyq => $question) 
                {

                    if (!empty($question["question_id"])) {

                        $exist_question = Question::find($question["question_id"]);

                        if (empty($exist_question)) {
                            array_push($isInValidField["isNotFoundField"]["questions"], $question["question_id"]);
                        }
                    }

                    if (empty($question["question_title"])) {
                        if (
                            (array_key_exists("question_title", $isInValidField["isRequiredField"]) &&
                                $isInValidField["isRequiredField"]["question_title"] != $question["number_order"]) ||
                            !array_key_exists("question_title", $isInValidField["isRequiredField"])
                        ) 
                        {
                            array_push($isInValidField["isRequiredField"]["question_title"], $question["number_order"]);
                        }
                    }

                    if (empty($question["question_type"]) || 
                        (isset($question["question_type"]) && $question["question_type"] != null && !in_array($question["question_type"], ['MultipleChoice', 'SingleChoice']))) {
                        if (
                            (array_key_exists("question_type", $isInValidField["isRequiredField"]) &&
                                $isInValidField["isRequiredField"]["question_type"] != $question["number_order"]) ||
                            !array_key_exists("question_type", $isInValidField["question_type"])
                        ) 
                        {
                            array_push($isInValidField["isRequiredField"]['question_type'], $question["number_order"]);
                        }
                    }

                    // if (isset($question["question_attachments"]) && count($question["question_attachments"])) {

                    //     foreach ($question["question_attachments"] as $key => $question_attachment) {
                    //         # code.
                    //         if (!is_array($question_attachment)) {
                    //             $isValidBase64 = $this->check_base64_image($question_attachment);
                    //             if (!$isValidBase64["isValidFormat"]) {
                    //                 if (
                    //                     (array_key_exists("$keyq", $isInValidField["isInvalidFormat"]["question_attachments"]) &&
                    //                         // $isInValidField["isInvalidFormat"]["question_attachments"] != $question["question_title"]
                    //                         !in_array($question["question_title"], $isInValidField["isInvalidFormat"]["question_attachments"])) ||
                    //                     !array_key_exists("$keyq", $isInValidField["isInvalidFormat"]["question_attachments"])
                    //                 ) {
                    //                     array_push($isInValidField["isInvalidFormat"]["question_attachments"], ["$keyq" => $question["question_title"]]);
                    //                 }
                    //             }

                    //             if (!$isValidBase64["isValidSize"]) {
                    //                 if (
                    //                     (array_key_exists("$keyq", $isInValidField["isInvalidSize"]["question_attachments"]) &&
                    //                         // $isInValidField["isInvalidSize"]["question_attachments"] != $question["question_title"]
                    //                         !in_array($question["question_title"], $isInValidField["isInvalidFormat"]["question_attachments"])) ||
                    //                     !array_key_exists("$keyq", $isInValidField["isInvalidSize"]["question_attachments"])
                    //                 ) {
                    //                     array_push($isInValidField["isInvalidSize"]["question_attachments"], ["$keyq" => $question["question_title"]]);
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }

                    if (empty($question["options"]) || (!empty($question["options"]) && !count($question["options"]))) {
                        if (!in_array('options', $isInValidField["isRequiredFieldCommon"])) {
                            array_push($isInValidField["isRequiredFieldCommon"], 'options');
                        }
                    }else {
                        $question_options = $question["options"];
                        $count_right_answer = 0;
                        foreach ($question_options as $key => $option) {

                            if (!empty($option["option_id"])) {

                                $exist_option = QuestionOption::find($option["option_id"]);

                                if (empty($exist_option)) {
                                    array_push($isInValidField["isNotFoundField"]["options"], $option["option_id"]);
                                }
                            }
                            
                            if (empty($option["option_body"])) 
                            {
                                if (
                                    (array_key_exists("option_body", $isInValidField["isRequiredField"]) &&
                                        $isInValidField["isRequiredField"]["option_body"] != $key) ||
                                    !array_key_exists("option_body", $isInValidField["option_body"])
                                ) 
                                {
                                    array_push($isInValidField["isRequiredField"]['option_body'], $key);
                                }
                                
                            }

                            if (!isset($option["right_answer"])) {
                                if (
                                    (array_key_exists("right_answer", $isInValidField["isRequiredField"]) &&
                                        $isInValidField["isRequiredField"]["right_answer"] != $key) ||
                                    !array_key_exists("right_answer", $isInValidField["right_answer"])
                                ) 
                                {
                                    array_push($isInValidField["isRequiredField"]['right_answer'], $key);
                                }
                            }else {
                                if ($option["right_answer"] == true) {
                                    $count_right_answer = $count_right_answer + 1;
                                }
                            }

                            // if (isset($option["option_attachments"]) && count($option["option_attachments"])) {

                            //     foreach ($option["option_attachments"] as $key => $option_attachment) {
                            //         # code.
                            //         if (!is_array($option_attachment)) {
                            //             $isValidBase64 = $this->check_base64_image($option_attachment);
                                        
                            //             if (!$isValidBase64["isValidFormat"]) {
                            //                 if (
                            //                     (array_key_exists("$key", $isInValidField["isInvalidFormat"]['option_attachments']) &&
                            //                         // $isInValidField["isInvalidFormat"]["option_attachments"] != $option["option_body"]) ||
                            //                         !in_array($option["option_body"], $isInValidField["isInvalidFormat"]["option_attachments"])) ||
                            //                     !array_key_exists("$key", $isInValidField["isInvalidFormat"]['option_attachments'])
                            //                 ) {
                            //                     array_push($isInValidField["isInvalidFormat"]['option_attachments'], ["$key" => $option["option_body"]]);
                            //                 }
                            //             }

                            //             if (!$isValidBase64["isValidSize"]) {
                            //                 if (
                            //                     (array_key_exists("$key", $isInValidField["isInvalidSize"]["option_attachments"]) &&
                            //                         // $isInValidField["isInvalidSize"]["option_attachments"] != $option["option_body"]) ||
                            //                         !in_array($option["option_body"], $isInValidField["isInvalidSize"]["option_attachments"])) ||
                            //                     !array_key_exists("$key", $isInValidField["isInvalidSize"])
                            //                 ) {
                            //                     array_push($isInValidField["isInvalidSize"]["option_attachments"], ["$key" => $option["option_body"]]);
                            //                 }
                            //             }
                            //         }
                            //     }
                            // }
                        }

                        if ($question["question_type"] == "SingleChoice" && ($count_right_answer > 1 || $count_right_answer == 0 )) {
                            if (
                                
                                (array_key_exists("$keyq", $isInValidField["isInvalidFormat"]["right_answer"]) &&
                                    // $isInValidField["isInvalidFormat"]["right_answer"] != $question["question_title"]) ||
                                !in_array($question["question_title"], $isInValidField["isInvalidFormat"]["right_answer"])) ||
                                !array_key_exists("$keyq", $isInValidField["isInvalidFormat"]["right_answer"])
                            ) {
                                array_push($isInValidField["isInvalidFormat"]["right_answer"], ["$keyq" => $question["question_title"]]);
                            }
                        }

                        if ($question["question_type"] == "MultipleChoice" && $count_right_answer == 0) {
                            if (
                                (array_key_exists("$keyq", $isInValidField["isInvalidFormat"]["right_answer"]) &&
                                    // $isInValidField["isInvalidFormat"]["right_answer"] != $question["question_title"]) ||
                                    !in_array($question["question_title"], $isInValidField["isInvalidFormat"]["right_answer"])) ||
                                !array_key_exists("$keyq", $isInValidField["isInvalidFormat"]["right_answer"])
                            ) {
                                array_push($isInValidField["isInvalidFormat"]["right_answer"], ["$keyq" => $question["question_title"]]);
                            }
                        }

                    }
                }
            }

            return $isInValidField;
        }
    }

    public function insertOrUpdateQuestion ($questions_data, $model) {
        // dd($model->survey_id);
        $survey_questions = $questions_data["questions"];
        $currentQuestions = $model->questions->toArray();

        // dd($survey_questions);

        $questionIds = [];
        foreach ($survey_questions as $key => $question) {
            if (isset($question["question_id"]) && $question["question_id"] != null) {
                array_push($questionIds, $question["question_id"]);
            }
        }

        $deleteQuestions = array_filter($currentQuestions, function ($var) use ($questionIds) {
            return !in_array($var['question_id'], $questionIds);
        });

        // dd($deleteQuestions);
        if (count($deleteQuestions)) {
            foreach ($deleteQuestions as $key => $question) {

                    Answer::where('question_id', $question["question_id"])->delete();
                    QuestionOption::where('question_id', $question["question_id"])->delete();
                    Question::find($question["question_id"])->delete();
            }
        }
        
        $percent_achieved_for_question = round(100 / count($survey_questions),2);
        // dd($percent_achieved_for_question);

        // dd($survey_questions);

        foreach ($survey_questions as $key => $question) {
            $attachments = $question["question_attachments"];

            // dd($survey_questions);

            if (isset($question["question_id"]) && $question["question_id"] != null) {
                $newQuestion = Question::find($question["question_id"]);

                $newQuestion->question_title = $question["question_title"];
                $newQuestion->question_description = $question["question_description"];
                $newQuestion->question_type = $question["question_type"];
                $newQuestion->number_order = $question["number_order"];
                $newQuestion->percent_achieved = $percent_achieved_for_question;
                $newQuestion->question_attachments = $attachments;
                $newQuestion->save();
            } else {
                $newQuestion = Question::create(
                    [
                        "question_title" => $question["question_title"],
                        "question_description" => $question["question_description"] ? $question["question_description"] : "",
                        "question_type" => $question["question_type"],
                        "survey_id" => $model->survey_id,
                        "number_order" => $question["number_order"],
                        "percent_achieved" => $percent_achieved_for_question,
                        "question_attachments" => $attachments,
                    ]
                );
            }
            // $newQuestion->number_order = $question->question_id ? $question->number_order : $number_order + 1;
            // $newQuestion->save();

            // if (isset($question["question_attachments"]) && count($question["question_attachments"]) &&isset($question["question_attachments"][0]) && !isset($question["question_attachments"][0]["url"])) {

            //     if (isset($newQuestion->question_attachments) && $newQuestion->question_attachments != null) {


            //         $question_attachments = $newQuestion->getMedia(MEDIA_COLLECTION["QUESTION_ATTACHMENTS"]);

            //         if (isset($question_attachments[0])) {
            //             $attachment_uuid = $question_attachments[0]->uuid;
            //             Media::where('model_id', $newQuestion->question_id)->where('uuid', $attachment_uuid)->delete();
            //         }
            //     }
            //     // upload file pending
            //     $attachments = $this->handleBase64Media($newQuestion, $question["question_attachments"], MEDIA_COLLECTION["QUESTION_ATTACHMENTS"]);
            //     if ($attachments) {

            //         $newQuestion->question_attachments = $attachments;
            //         $newQuestion->has_attachment = true;

            //         $newQuestion->save();
            //     }
            // }else {

            //     if ((isset($question["question_attachments"][0]) && !isset($question["question_attachments"][0]["url"])) || !count($question["question_attachments"])) {

            //         $question_attachments = $newQuestion->getMedia(MEDIA_COLLECTION["QUESTION_ATTACHMENTS"]);

            //         if (isset($question_attachments[0])) {
            //             $attachment_uuid = $question_attachments[0]->uuid;
            //             Media::where('model_id', $newQuestion->question_id)->where('uuid', $attachment_uuid)->delete();
            //         }
            //     }

            // }

            $question_options = $question["options"];
            $currentOptions = $newQuestion->options->toArray();

            $optionIds = [];
            foreach ($question_options as $key => $question_option) {
                if (isset($question_option["option_id"]) && $question_option["option_id"] != null) {
                    array_push($optionIds, $question_option["option_id"]);
                }
            }

            $deleteOptions = array_filter($currentOptions, function ($var) use ($optionIds) {
                return !in_array($var['option_id'], $optionIds);
            });


            if (count($deleteOptions)) {
                foreach ($deleteOptions as $key => $option) {
                    QuestionOption::find($option["option_id"])->delete();
                }
            }

            $rightOptions = array_filter($question_options, function ($var) {

                return $var["right_answer"]; 
            });

            // dd($question_options);

            foreach ($question_options as $key => $option) {

                // dd($option["option_attachmant_name"]);

                $option_attachments = $option["option_attachments"];
                # code...
                if (isset($option["option_id"]) && $option["option_id"] != null) {

                    // dd('111111');

                    $newOption = QuestionOption::find($option["option_id"]);

                    $newOption->option_body = $option["option_body"];
                    $newOption->right_answer = $option["right_answer"];
                    $newOption->option_attachmant_name = $option["option_attachmant_name"];
                    $newOption->option_attachments = $option_attachments;
                    $newOption->save();
                } else {

                    // dd('222222');

                    $newOption = QuestionOption::create(
                        [
                            "option_body" => $option["option_body"],
                            "right_answer" => $option["right_answer"],
                            "option_attachmant_name" => $option["option_attachmant_name"],
                            "question_id" => $newQuestion["question_id"],
                            "option_attachments" => $option_attachments,
                        ]
                    );
                }

                // if (isset($option["option_attachments"]) && count($option["option_attachments"]) && isset($option["option_attachments"][0]) && !isset($option["option_attachments"][0]["url"])) {
                //     if (isset($newOption->option_attachments) && $newOption->option_attachments != null) {
                //         $option_attachments = $newOption->getMedia(MEDIA_COLLECTION["OPTION_ATTACHMENTS"]);

                //         if (isset($option_attachments[0])) {
                //             $attachment_uuid = $option_attachments[0]->uuid;
                //             Media::where('model_id', $newOption->option_id)->where('uuid', $attachment_uuid)->delete();
                //         }

                //     }
                //     // upload file pending
                //     $attachments = $this->handleBase64Media($newOption, $option["option_attachments"], MEDIA_COLLECTION["OPTION_ATTACHMENTS"]);
                //     if ($attachments) {

                //         $newOption->option_attachmant_name = $option["option_attachmant_name"];
                //         $newOption->option_attachments = $attachments;
                //         $newOption->is_image = true;

                //         $newOption->save();
                //     }
                // }else {
                //     if ((isset($option["option_attachments"][0]) && !isset($option["option_attachments"][0]["url"])) || !count($option["option_attachments"])) {

                //         $option_attachments = $newOption->getMedia(MEDIA_COLLECTION["OPTION_ATTACHMENTS"]);

                //         if (isset($option_attachments[0])) {
                //             $attachment_uuid = $option_attachments[0]->uuid;
                //             Media::where('model_id', $newOption->option_id)->where('uuid', $attachment_uuid)->delete();
                //         }
                //     }


                // }
            }
        }
    }
}

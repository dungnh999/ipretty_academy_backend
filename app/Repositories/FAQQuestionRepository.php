<?php

namespace App\Repositories;

use App\Models\FAQQuestion;
use App\Repositories\BaseRepository;

/**
 * Class FAQQuestionRepository
 * @package App\Repositories
 * @version October 27, 2021, 2:49 pm +07
*/

class FAQQuestionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'question_name',
        'answer_name',
        'number_order',
        'faq_id'
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
        return FAQQuestion::class;
    }


}

<?php

namespace App\Repositories;

use App\Models\QuestionOption;
use App\Repositories\BaseRepository;

/**
 * Class QuestionOptionRepository
 * @package App\Repositories
 * @version September 18, 2021, 10:40 am UTC
*/

class QuestionOptionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'question_id',
        'option_body',
        'right_answer',
        'option_attachments',
        'is_image'
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
        return QuestionOption::class;
    }

    public function destroyMedia($media, $id) {
        
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $mediaUrl = $media->getUrl();

        $optionAttachments = explode(',', $model->question_attachments);

        $optionAttachments = array_diff_key($optionAttachments, [$mediaUrl]);

        $model->question_attachments = implode(',', $optionAttachments);

        if (!count($optionAttachments)) {

            $model->has_attachment = false;
        }

        $model->save();

        $media->delete();

        return $model;
    }
}

<?php

namespace App\Repositories;

use App\Models\Question;
use App\Repositories\BaseRepository;

/**
 * Class QuestionRepository
 * @package App\Repositories
 * @version September 18, 2021, 10:34 am UTC
*/

class QuestionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'question_title',
        'question_description',
        'question_type',
        'number_order',
        'question_attachments',
        'has_attachment',
        'session_id'
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
        return Question::class;
    }

    public function destroyMedia($media, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $mediaUrl = $media->getUrl();

        $questionAttachments = explode(',', $model->question_attachments);

        $questionAttachments = array_diff_key($questionAttachments, [$mediaUrl]);

        $model->question_attachments = implode(',', $questionAttachments);

        if (!count($questionAttachments)) {
            
            $model->has_attachment = false;
        }

        $model->save();

        $media->delete();

        return $model;
    }
}

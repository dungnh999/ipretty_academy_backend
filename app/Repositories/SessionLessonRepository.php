<?php

namespace App\Repositories;

use App\Models\SessionLesson;
use App\Repositories\BaseRepository;

/**
 * Class SessionLessonRepository
 * @package App\Repositories
 * @version September 8, 2021, 5:57 pm UTC
*/

class SessionLessonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'session_id',
        'lesson_id',
        'count_views'
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
        return SessionLesson::class;
    }
}

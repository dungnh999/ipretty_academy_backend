<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\CourseLessonChapter;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

// use FFMpeg;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class LessonRepository
 * @package App\Repositories
 * @version September 8, 2021, 5:13 pm UTC
 */
class CourseLessonChapterReposity extends BaseRepository
{
    use CommonBusiness;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'course_id',
        'chapter_id',
        'lesson_id',
        'priority',
        'position',
        'uuid',
    ];

    protected $relations = ['user'];

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
        return CourseLessonChapter::class;
    }

    public function step($request = null)
    {
        $query = $this->model->newQuery();
        return $query->where('uuid', $request->get('id'))->first();
    }

}

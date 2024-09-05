<?php

namespace App\Repositories;

use App\Models\Chapter;
use App\Repositories\BaseRepository;

/**
 * Class ChapterRepository
 * @package App\Repositories
 * @version September 8, 2021, 4:34 pm UTC
 */

class ChapterRepository extends BaseRepository
{
  /**
   * @var array
   */
  protected $fieldSearchable = ['chapter_name', 'course_id', 'course_version'];

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
    return Chapter::class;
  }

  public function getAllChapterCourse($params = null)
  {
    return Chapter::where('chapters.course_id', $params['course_id'])->get();
  }


  public function create($request)
  {
    $newChapter = Chapter::create([
      'chapter_name' => $request['chapter_name'],
      'course_id' => $request->course_id,
      'course_version' => 1,
      'number_order' => $request['number_order'] ? $request['number_order'] : 0,
    ]);

    $newChapter->save();
    return $newChapter;
  }
}

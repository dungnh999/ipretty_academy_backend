<?php

namespace App\Repositories;

use App\Models\Session;
use App\Repositories\BaseRepository;

/**
 * Class SessionRepository
 * @package App\Repositories
 * @version September 8, 2021, 5:32 pm UTC
*/

class SessionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'session_name',
        'chapter_id'
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
        return Session::class;
    }
}

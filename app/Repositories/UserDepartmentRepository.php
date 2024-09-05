<?php

namespace App\Repositories;

use App\Models\UserDepartment;
use App\Repositories\BaseRepository;

/**
 * Class UserDepartmentRepository
 * @package App\Repositories
 * @version September 6, 2021, 4:40 am UTC
*/

class UserDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'department_name'
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
        return UserDepartment::class;
    }
}

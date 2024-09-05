<?php

namespace App\Repositories;

use App\Models\UserActivateDiscountCode;
use App\Repositories\BaseRepository;

/**
 * Class UserActivateDiscountCodeRepository
 * @package App\Repositories
 * @version November 28, 2021, 2:22 pm +07
*/

class UserActivateDiscountCodeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
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
        return UserActivateDiscountCode::class;
    }

    // public function usedDiscountCode($input) {
    //     $model = $this->model->newInstance($input);
    //     return $model->        
    // }
}

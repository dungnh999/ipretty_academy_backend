<?php

namespace App\Repositories;

use App\Models\Certificate;
use App\Repositories\BaseRepository;

/**
 * Class CertificateRepository
 * @package App\Repositories
 * @version September 7, 2021, 5:30 pm UTC
*/

class CertificateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'certificate_name',
        'certificate_image',
        'certificate_description'
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
        return Certificate::class;
    }
}

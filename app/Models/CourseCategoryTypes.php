<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategoryTypes extends Model
{
    use HasFactory;

    public $table = 'course_categories_types';

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $fillable = [
      'category_type_name',
      'category_type_description',
      'created_by',
      'created_at',
      'updated_at',
      'isPublished',
    ];

  /**
   * The attributes that should be casted to native types.
   *
   * @var array
   */
  protected $casts = [
    'isPublished' => 'boolean',
    'category_id' => 'integer',
    'category_name' => 'string',
    'course_category_attachment' => 'string',
    'created_by' => 'integer',
    'created_at' => 'datetime:Y-m-d H:i',
    'updated_at' => 'datetime:Y-m-d H:i',
  ];

  /**
   * Validation rules
   *
   * @var array
   */
    public static $rules = [

    ];

    public function createdBy()
    {
      return $this->belongsTo('App\Models\User', 'created_by');
    }
}

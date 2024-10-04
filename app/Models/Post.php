<?php

namespace App\Models;

use App\Contract\CommonBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\HasMedia;

class Post extends Model implements HasMedia
{
    use HasFactory;

    use SoftDeletes;

    use InteractsWithMedia;

    use CommonBusiness;

    protected $table = 'posts';
    protected $dates = ['deleted_at'];

    protected $mediaCollection = MEDIA_COLLECTION["POST_BANNERURL"];


    protected $fillable = [
        'title',
        'content',
        'bannerUrl',
        'external_url',
        'slug',
        'introduction',
        'sub_introduction',
        'color_introduction',
        'bg_color_button',
        'color_button',
        'color_content',
        'color_title',
        'created_by',
        'category_id',
        'is_active',
        'is_banner',
        'isTrademark',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'post_id' => 'integer',
        'title' => 'string',
        'content' => 'string',
        'external_url' => 'string',
        'bannerUrl' => 'string',
        'introduction' => 'string',
        'sub_introduction' => 'string',
        'color_introduction' => 'string',
        'color_title' => 'string',
        'color_content' => 'string',
        'bg_color_button' => 'string',
        'color_button' => 'string',
        'slug' => 'string',
        'created_by' => 'integer',
        'category_id' => 'integer',
        'is_active' => 'boolean',
        'is_banner' => 'boolean',
        'isTrademark' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    public static $rules = [
        'title' => 'required',
    ];

    protected $primaryKey = 'post_id';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->mediaCollection)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/jfif', 'image/webp']);
    }

    public function handleMedia($request = null)
    {
      if ($request == null) {
            return;
      }

      // Store Image
      if ($request->hasFile($this->mediaCollection) && $request->file($this->mediaCollection)->isValid()) {
          $newMedia = $this->addMediaFromRequest($this->mediaCollection)->toMediaCollection($this->mediaCollection);
          $this[$this->mediaCollection] = str_replace(public_path(), '', $newMedia->getPath());
          $this->save(); //remember to save again
      } else {
          // TODO: throw exception
      }
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}

<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @SWG\Definition(
 *      definition="Certificate",
 *      required={""},
 *      @SWG\Property(
 *          property="certificate_id",
 *          description="certificate_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="certificate_name",
 *          description="certificate_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="certificate_image",
 *          description="certificate_image",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="certificate_description",
 *          description="certificate_description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class Certificate extends Model implements HasMedia
{
    use SoftDeletes;

    use InteractsWithMedia;

    public $table = 'certificates';

    protected $primaryKey = 'certificate_id';

    protected $dates = ['deleted_at'];

    protected $mediaCollection = MEDIA_COLLECTION["CERTIFICATE_IMAGE"];

    public $fillable = [
        'certificate_name',
        'certificate_image',
        'certificate_description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'certificate_id' => 'integer',
        'certificate_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'certificate_name' => 'required',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->mediaCollection)
        ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;
    }

    // remember to save before run this method
    public function handleMedia($request = null): void
    {
        if ($request == null) {
            return;
        }
        // Store Image
        if ($request->hasFile('certificate_image') && $request->file('certificate_image')->isValid()) {
            $this->addMediaFromRequest('certificate_image')->toMediaCollection($this->mediaCollection);
            $this->certificate_image = $this->getFirstMediaUrl($this->mediaCollection);
            $this->save(); //remember to save again
        } else {
            // TODO: throw exception
        }
    }

    
}

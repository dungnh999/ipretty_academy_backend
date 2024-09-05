<?php

namespace App\Models;

use App\Contract\CommonBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use HasFactory;
    use CommonBusiness;
    use SoftDeletes;

    protected $table = 'post_categories';

    protected $dates = ['deleted_at'];

    const Post_Category_Name = 'Phản hồi khách hàng';
    const about_us_category = 'Về chúng tôi';
    const news_category = 'Tin tức';
    const recruitment_category = 'Cơ hội nghề nghiệp';
    const about_ipretty = 'Về Ipretty Edu';
    const team_of_experts = 'Đội ngũ chuyên gia';
    const terms_policy = 'Điều khoản và điều kiện';
    const course_training = 'Khoá học và đào tạo';

    protected $fillable = [
        'category_slug', 'category_name', 'description', 'created_at', 'updated_at', 'isPublished'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    protected $primaryKey = 'category_id';

    public $timestamps = true;

    public function posts(){
        return $this->hasMany(Post::class, 'category_id');
    }
}

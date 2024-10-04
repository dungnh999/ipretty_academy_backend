<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $folder = 'default';
        switch ($media->collection_name) {
            case MEDIA_COLLECTION['POST_BANNERURL']:
                $folder = 'banner';
                break;
            case MEDIA_COLLECTION['USER_AVATAR']:
                $folder = 'avatar';
                break;
            case MEDIA_COLLECTION['COURSE_FEATURE_IMAGE']:
                $folder = 'course';
                break;
            case MEDIA_COLLECTION['COURSE_CATEGORY_ATTACHMENT']:
                $folder = 'category';
                break;
        }
        // Tạo đường dẫn tùy chỉnh cho media, ví dụ theo slug
        return $folder . '/' . md5($media->id) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        // Tạo đường dẫn cho các phiên bản chuyển đổi của hình ảnh
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        // Tạo đường dẫn cho hình ảnh responsive
        return $this->getPath($media) . 'responsive-images/';
    }
}

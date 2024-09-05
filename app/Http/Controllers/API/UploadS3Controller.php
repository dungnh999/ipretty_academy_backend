<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Jobs\S3GetLinkJob;
use App\Jobs\S3UploadJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadS3Controller extends AppBaseController
{
  public function uploadFile(Request $request)
  {
    $file = $request->file('file');
    $media_type = $file->getClientMimeType();
    $type = $request->file('type');
    $key = Str::random(40);
    $filename = $key . '.' . $file->getClientOriginalExtension();
    $file->move(storage_path('app/temp'), $filename);

    if (str_starts_with($media_type, 'image/')) {
      $folder = TEXT_URL_IMAGE;
    } elseif (str_starts_with($media_type, 'video/')) {
      // Đây là video
      $folder = TEXT_URL_VIDEO;
    } else {
      // Đây có thể là loại tệp tin khác
      $folder = TEXT_URL_FILES;
    }
    switch ($type){
      case ENUM_COURSE:
        $folder .= TEXT_PATH_COURSE;
        break;
      case ENUM_BANNER:
        $folder .= TEXT_PATH_BANNER;
        break;
      case ENUM_BLOG:
        $folder .= TEXT_PATH_BLOG;
        break;
      case ENUM_ACCOUNT:
        $folder .= TEXT_PATH_ACCOUNT;
        break;
      default:
        $folder .= TEXT_PATH_COURSE;
    }
    S3GetLinkJob::dispatchSync($filename, $folder , $type);
    return $this->sendResponse(
      ['key' => $filename , 'full_url' => env('AWS_URL_UPLOAD') . $folder . $filename],TEXT_UPLOAD_SUCCESSFULL
    );
  }

  public function uploadFileLocal($fileLocal, $typeLocal = 0)
  {
    $file = $fileLocal;
    $media_type = $file->getClientMimeType();
    $type = $typeLocal;
    $key = Str::random(40);
    $filename = $key . '.' . $file->getClientOriginalExtension();
    $file->move(storage_path('app/temp'), $filename);

    if (str_starts_with($media_type, 'image/')) {
      $folder = TEXT_URL_IMAGE;
    } elseif (str_starts_with($media_type, 'video/')) {
      // Đây là video
      $folder = TEXT_URL_VIDEO;
    } else {
      // Đây có thể là loại tệp tin khác
      $folder = TEXT_URL_FILES;
    }
    switch ($type){
      case ENUM_COURSE:
        $folder .= TEXT_PATH_COURSE;
        break;
      case ENUM_BANNER:
        $folder .= TEXT_PATH_BANNER;
        break;
      case ENUM_BLOG:
        $folder .= TEXT_PATH_BLOG;
        break;
      case ENUM_ACCOUNT:
        $folder .= TEXT_PATH_ACCOUNT;
        break;
      default:
        $folder .= TEXT_PATH_COURSE;
    }
    S3GetLinkJob::dispatchSync($filename, $folder , $type);
    return ['key' => $filename , 'full_url' => env('AWS_URL_UPLOAD') . $folder . $filename];
  }
}

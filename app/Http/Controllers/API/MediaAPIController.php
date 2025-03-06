<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\MediaResource;
use App\Repositories\MediaRepository;
use Illuminate\Http\Request;

class MediaAPIController extends AppBaseController
{

    private $mediaRepository;
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }


    public function index(Request $request)
    {
        $media = $this->mediaRepository->getAllMedia($request);
        return $this->sendResponse(
            $media,
            __('messages.retrieved', ['model' => __('models/learningProcesses.plural')])
        );
    }
}

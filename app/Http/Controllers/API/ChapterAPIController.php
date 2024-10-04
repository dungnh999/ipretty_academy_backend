<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChapterAPIRequest;
use App\Http\Requests\API\UpdateChapterAPIRequest;
use App\Models\Chapter;
use App\Repositories\ChapterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\ChapterResource;
use Response;

/**
 * Class ChapterController
 * @package App\Http\Controllers\API
 */

class ChapterAPIController extends AppBaseController
{
    /** @var  ChapterRepository */
    private $chapterRepository;

    public function __construct(ChapterRepository $chapterRepo)
    {
        $this->chapterRepository = $chapterRepo;
    }

    public function index(Request $request)
    {
        $chapters = $this->chapterRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            ChapterResource::collection($chapters),
            __('messages.retrieved', ['model' => __('models/chapters.plural')])
        );
    }

    public function store(CreateChapterAPIRequest $request)
    {
        $input = $request->all();

        $chapter = $this->chapterRepository->create($input);

        return $this->sendResponse(
            new ChapterResource($chapter),
            __('messages.saved', ['model' => __('models/chapters.singular')])
        );
    }

    public function show($id)
    {
        /** @var Chapter $chapter */
        $chapter = $this->chapterRepository->find($id);

        if (empty($chapter)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/chapters.singular')])
            );
        }

        return $this->sendResponse(
            new ChapterResource($chapter),
            __('messages.retrieved', ['model' => __('models/chapters.singular')])
        );
    }

    public function update($id, UpdateChapterAPIRequest $request)
    {
        $input = $request->all();

        /** @var Chapter $chapter */
        $chapter = $this->chapterRepository->find($id);

        if (empty($chapter)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/chapters.singular')])
            );
        }

        $chapter = $this->chapterRepository->update($input, $id);

        return $this->sendResponse(
            new ChapterResource($chapter),
            __('messages.updated', ['model' => __('models/chapters.singular')])
        );
    }

    public function destroy($id)
    {
        /** @var Chapter $chapter */
        $chapter = $this->chapterRepository->find($id);

        if (empty($chapter)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/chapters.singular')])
            );
        }

        $chapter->delete();
        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/chapters.singular')])
        );
    }
}

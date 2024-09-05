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

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chapters",
     *      summary="Get a listing of the Chapters.",
     *      tags={"Chapter"},
     *      description="Get all Chapters",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Chapter")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @param CreateChapterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chapters",
     *      summary="Store a newly created Chapter in storage",
     *      tags={"Chapter"},
     *      description="Store Chapter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Chapter that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Chapter")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Chapter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChapterAPIRequest $request)
    {
        $input = $request->all();

        $chapter = $this->chapterRepository->create($input);

        return $this->sendResponse(
            new ChapterResource($chapter),
            __('messages.saved', ['model' => __('models/chapters.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chapters/{id}",
     *      summary="Display the specified Chapter",
     *      tags={"Chapter"},
     *      description="Get Chapter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Chapter",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Chapter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @param int $id
     * @param UpdateChapterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chapters/{id}",
     *      summary="Update the specified Chapter in storage",
     *      tags={"Chapter"},
     *      description="Update Chapter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Chapter",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Chapter that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Chapter")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Chapter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

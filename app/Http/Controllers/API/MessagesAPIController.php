<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMessagesAPIRequest;
use App\Http\Requests\API\UpdateMessagesAPIRequest;
use App\Models\Messages;
use App\Repositories\MessagesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\MessagesResource;
use Response;

/**
 * Class MessagesController
 * @package App\Http\Controllers\API
 */

class MessagesAPIController extends AppBaseController
{
    /** @var  MessagesRepository */
    private $messagesRepository;

    public function __construct(MessagesRepository $messagesRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->messagesRepository = $messagesRepo;
    }

    /**
     * Display a listing of the Messages.
     * GET|HEAD /messages
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $messages = $this->messagesRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            MessagesResource::collection($messages),
            __('messages.retrieved', ['model' => __('models/messages.plural')])
        );
    }

    /**
     * Store a newly created Messages in storage.
     * POST /messages
     *
     * @param CreateMessagesAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateMessagesAPIRequest $request)
    {
        $input = $request->all();
        $user = auth()->user();
        $messages = $this->messagesRepository->createMessages($input,$user->id,$request);

        return $this->sendResponse(
            new MessagesResource($messages),
            __('messages.saved', ['model' => __('models/messages.singular')])
        );
    }

    /**
     * Display the specified Messages.
     * GET|HEAD /messages/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Messages $messages */
        $messages = $this->messagesRepository->find($id);

        if (empty($messages)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/messages.singular')])
            );
        }

        return $this->sendResponse(
            new MessagesResource($messages),
            __('messages.retrieved', ['model' => __('models/messages.singular')])
        );
    }

    /**
     * Update the specified Messages in storage.
     * PUT/PATCH /messages/{id}
     *
     * @param int $id
     * @param UpdateMessagesAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMessagesAPIRequest $request)
    {
        $input = $request->all();

        /** @var Messages $messages */
        $messages = $this->messagesRepository->find($id);

        if (empty($messages)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/messages.singular')])
            );
        }

        $messages = $this->messagesRepository->update($input, $id);

        return $this->sendResponse(
            new MessagesResource($messages),
            __('messages.updated', ['model' => __('models/messages.singular')])
        );
    }

    /**
     * Remove the specified Messages from storage.
     * DELETE /messages/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Messages $messages */
        $messages = $this->messagesRepository->find($id);

        if (empty($messages)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/messages.singular')])
            );
        }

        $messages->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/messages.singular')])
        );
    }

    public function showAllMessagesPrivate($user_id){
        $messages = $this->messagesRepository->showAllMessagesPrivate($user_id);

        return $this->sendResponse(
            MessagesResource::collection($messages),
            __('messages.show_messenger_detail_successfully')
        );
    }

    public function getListStudentForTeacher()
    {
        $params = request()->query();
        $listStudents = $this->messagesRepository->getListStudentForTeacher($params);

        return $this->sendResponse(
            $listStudents,
            __('messages.get_list_student_successfully')
        );
    }

    public function getListTeacherForStudent()
    {
        $params = request()->query();
        $listStudents = $this->messagesRepository->getListTeacherForStudent($params);

        return $this->sendResponse(
            $listStudents,
            __('messages.get_list_teacher_successfully')
        );
    }


    public function deleteMessengers($receiver_id)
    {   
        $user = auth()->user();

        $is_delete = $this->messagesRepository->checkContentDeleteMessenger($user->id,$receiver_id);

        // dd($is_delete);

        if(!$is_delete){

            return $this->sendSuccess(

                __('messages.deleted', ['model' => __('models/messages.singular')])

            );

        }
        
        $this->messagesRepository->deleteMessengers($user->id,$receiver_id,$is_delete);
        
        return $this->sendSuccess(
            __('messages.deleted', ['model' => __('models/messages.singular')])
        );
    }

    public function getListChat()
    {

        $params = request()->query();

        $user = auth()->user();
        $listUsers = $this->messagesRepository->getListChat($params);
        // if ($user->hasRole(['employee']) && $user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
        //     $listUsers = array_merge($listUsers->toArray(), $this->messagesRepository->getListStudentForTeacher($params)->toArray());
        // }

        return $this->sendResponse(
            $listUsers,
            __('messages.get_list_chat_successfully')
        );
    }

    public function receiverSeen($user_id) {
        
        $messages = $this->messagesRepository->receiverSeen($user_id);

        return $this->sendSuccess(
            __('messages.updated', ['model' => __('models/messages.singular')])
        );

    }

    public function countUnreadMessages() {

        $user = auth()->user();

        $countUnreactMess = $this->messagesRepository->countUnreadMessages($user);

        return $this->sendResponse(
            $countUnreactMess,
            __('messages.get_number_unread_successfully')
        );

    }
}

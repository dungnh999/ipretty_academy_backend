<?php

namespace App\Repositories;

use App\Models\Messages;
use App\Repositories\BaseRepository;
use App\Contract\CommonBusiness;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Models\UserMessageStatus;
use App\Models\Users;

/**
 * Class MessagesRepository
 * @package App\Repositories
 * @version October 14, 2021, 10:49 am +07
 */

class MessagesRepository extends BaseRepository
{
  use CommonBusiness;
  /**
   * @var array
   */
  protected $fieldSearchable = [
    'sender_id',
    'body',
    'type',
    'is_attachment',
    'receiver_id',
    'receiver_seen',
  ];

  protected $fieldSearchableListChat = [
    'name'
  ];

  /**
   * Return searchable fields
   *
   * @return array
   */
  public function getFieldsSearchable()
  {
    return $this->fieldSearchable;
  }

  public function fieldSearchableListChat()
  {
    return $this->fieldSearchableListChat;
  }

  /**
   * Configure the Model
   **/
  public function model()
  {
    return Messages::class;
  }

  public function createMessages($data, $user_id, $request)
  {

    $model = $this->model->newInstance($data);
    $model->sender_id = $user_id;
    $model->receiver_id = $data['receiver_id'];
    $model->body = $data['body'];
    $model->save();
    $model->handleMedia($request);
    $usermessagestatus = UserMessageStatus::where('user_id', $user_id)
      ->where('partner_id', $data['receiver_id'])
      ->first();
    if (!$usermessagestatus) {
      $usermessagestatus = new UserMessageStatus();
    }
    $usermessagestatus->user_id = $user_id;
    $usermessagestatus->partner_id = $data['receiver_id'];
    $usermessagestatus->lasted_message_seen_id = $model->id;
    $usermessagestatus->save();
    // $query = $this->model->newQuery()->where('sender_id', '=', $user_id)->where('receiver_id', '=', $data['receiver_id'])->where('receiver_seen', '=', 0);
    // $query_all = $this->model->newQuery()->where('receiver_id', '=', $data['receiver_id'])->where('receiver_seen', '=', 0)->get();
    // $messages = $query->get();
    // $count_receiver_seen = count($messages);
    // $count_all_receiver_seen = count($query_all);
    // event(new \App\Events\NewMessageSent($model, $data['receiver_id'], $count_receiver_seen, $count_all_receiver_seen));
    // event(new \App\Events\GetListEventChat($data['receiver_id']));
    // event(new \App\Events\GetListChatBySeender($user_id));
    // event(new \App\Events\AllMessagesReceiverSeen($data['receiver_id'], $count_all_receiver_seen));
    return $model;
  }

  public function countUnreadMessages($user)
  {

    $query_all = $this->model->newQuery()->where('receiver_id', '=', $user->id)->where('receiver_seen', '=', 0)->get();

    $count_all_receiver_seen = count($query_all);

    return $count_all_receiver_seen;
  }

  public function showAllMessagesPrivate($user_id)
  {
    $user = auth()->user();

    $get_last_mess  = UserMessageStatus::where('user_id', $user->id)->where('partner_id', $user_id)->first();

    // dd($get_last_mess);

    $messages = Messages::where(function ($query) use ($user, $user_id) {
      $query->where('sender_id', $user->id)->Where('receiver_id', $user_id)
        ->orwhere('sender_id', $user_id)->Where('receiver_id', $user->id);
    })
      ->orderBy('created_at', 'asc')
      ->select('id', 'body', 'created_at', 'receiver_seen', 'is_attachment', 'updated_at', 'sender_id');

    if ($get_last_mess) {

      // dd($messages);

      // var_dump($get_last_mess->delete_id_mess);

      if ($get_last_mess->delete_id_mess != null) {

        $messages = $messages->where('id', '>', $get_last_mess->delete_id_mess);

        // dd($messages);

      }
    }

    $messages = $messages->get();

    // dd($messages);

    if (count($messages) > 0) {
      $lastedMessage = $messages->last();
      $usermessagestatus = UserMessageStatus::where('user_id', $user->id)->where('partner_id', $user_id)->first();
      if (!$usermessagestatus) {
        $usermessagestatus = new UserMessageStatus();
      }
      $usermessagestatus->user_id = $user->id;
      $usermessagestatus->partner_id = $user_id;
      $usermessagestatus->lasted_message_seen_id = $lastedMessage->id;
      $usermessagestatus->save();

      // $count_usermessagestatus = UserMessageStatus::where('lasted_message_seen_id', $lastedMessage->id)->count();
      // if($count_usermessagestatus == 2){
      //     $messages_update = Messages::where('id', $lastedMessage->id)->first();
      //     $messages_update->receiver_seen = 1;
      //     $messages_update->save();
      // }
    }
    return $messages;
  }
  public function getNewReceivedMessages () {
    $user = auth()->user();
    $newReceiveMessage = UserMessageStatus::join('messages', 'lasted_message_seen_id', '=', 'messages.id')->where('receiver_id', '=', $user->id)->where('partner_id', '=', $user->id)
    ->where(function($q) {
      $q->orwhereNull('delete_id_mess')
      ->orWhereRaw('delete_id_mess < lasted_message_seen_id');
    })->pluck('user_id')->toArray();
    return $newReceiveMessage;
  }
  public function getListChat($params = null)
  {
    $user = auth()->user();
    $partnerIds = $user->courses->pluck('teacher_id')->toArray();
    if ($user->hasRole("employee") && $user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
      $partnerIds = array_merge($partnerIds, $user->studentsFollowTeacher->pluck("student_id")->toArray());
    }
    $newSenderIds = $this->getNewReceivedMessages();
    $partnerChattedIds = array_unique(array_merge($newSenderIds, $user->messagesSendedIsNotDelete->pluck("partner_id")->toArray()));

    if (!empty($params['keyword'])) {
      $partners = User::whereIn('id', $partnerIds)->where('id', '!=', $user->id)
        ->where(function ($q) use ($params) {
          $q->orWhere('name', 'like', '%' . $params['keyword'] . '%')
            ->orWhere('email', 'like', '%' . $params['keyword'] . '%');
        })->withTrashed()->select('id', 'email', 'name', 'avatar', 'latest_active_at', 'isLocked', 'email_verified_at');
    } else {
      $ids_ordered = implode(',', $partnerChattedIds);
      $partners = User::whereIn('id', $partnerChattedIds)->where('id', '!=', $user->id)->withTrashed()->select('id', 'email', 'name', 'avatar', 'latest_active_at', 'isLocked', 'email_verified_at');
      if (count($partnerChattedIds) > 0) {
        $partners = $partners->orderByRaw("FIELD(id, $ids_ordered)");
      }
    }
    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {
        $perpage = $params['perpage'];
        $partners = $partners->paginate($perpage);
      } else {
        $partners = $partners->paginate(PERPAGE);
      }
    } else {
      $partners = $partners->get();
    }
    foreach ($partners as $partner) {
      $latestMessageReceived = $this->getListChatLast($user->id, $partner->id);
      $allMessageSendedUnread = $this->model->newQuery()->where('sender_id', '=', $partner->id)->where('receiver_id', '=', $user->id)->where('receiver_seen', '=', 0)->get();
      if ($latestMessageReceived != NULL) {
        $statusLatestMessage = $latestMessageReceived->statusLatestMessage($user->id);
        $latestMessage = $latestMessageReceived->body;
        if ($statusLatestMessage != NULL && (($statusLatestMessage->delete_id_mess >= $statusLatestMessage->lasted_message_seen_id) && $statusLatestMessage->delete_id_mess != NULL)) {
          $latestMessage = null;
        } 
        $partner['unread_messages'] = count($allMessageSendedUnread);
        if ($user->id == $latestMessageReceived->sender_id) {
          $partner['is_seen'] = 1;
        } else {
          $partner['is_seen'] = $latestMessageReceived->receiver_seen;
        }
        $partner['mess_last'] = $latestMessage;
        $partner['last_message_sent_time'] = $latestMessageReceived->created_at->format('Y-m-d H:i:s');
      } else {
        $partner['mess_last'] = null;
        $partner['is_seen'] = null;
        $partner['last_message_sent_time'] = null;
      }
      if ($partner['latest_active_at'] != null && $partner['latest_active_at'] >= now()->subMinutes(10)) {
        $partner['is_online'] = true;
      } else {
        $partner['is_online'] = false;
      }
    }
    $partners = $partners->toArray();
    usort($partners, function ($partner, $partner_next) {
      return strtotime($partner_next['last_message_sent_time']) - strtotime($partner['last_message_sent_time']);
    });
    return $partners;
  }

  public function getListStudentForTeacher($params = null)
  {

    $user = auth()->user();

    $list_students = $user->studentsFollowTeacher->pluck('student_id')->toArray();

    $users_message_status = $user->messagesSendedIsNotDelete->toArray();
    $new_users_message_status = [];

    foreach ($users_message_status as $user_message_status) {

      if ($user_message_status['lasted_message_seen_id'] > $user_message_status['delete_id_mess']) {

        array_push($new_users_message_status, $user_message_status['partner_id']);
      }
    }
    if (!empty($params['keyword'])) {

      $user_all = User::whereIn('id', $list_students)->where('id', '!=', $user->id)
        ->where(function ($q) use ($params) {
          $q->orWhere('name', 'like', '%' . $params['keyword'] . '%')
            ->orWhere('email', 'like', '%' . $params['keyword'] . '%');
        })->withTrashed();
    } else {

      $ids_ordered = implode(',', $new_users_message_status);

      $user_all = User::whereIn('id', $new_users_message_status)->where('id', '!=', $user->id)->withTrashed();

      if (count($new_users_message_status) > 0) {

        $user_all = $user_all->orderByRaw("FIELD(id, $ids_ordered)");
      }
    }

    if (isset($params['paging']) && $params['paging'] == true) {

      if (isset($params['perpage']) && $params['perpage'] != null) {

        $perpage = $params['perpage'];

        $user_all = $user_all->paginate($perpage);
      } else {

        $user_all = $user_all->paginate(PERPAGE);
      }
    } else {

      $user_all = $user_all->get();
    }

    foreach ($user_all as $dbres) {

      $get_mess = $this->getListChatLast($user->id, $dbres->id);

      $get_all_mes = $this->model->newQuery()->where('sender_id', '=', $user->id)->where('receiver_id', '=', $dbres->id)->where('receiver_seen', '=', 0)->get();

      if ($get_mess != NULL) {

        $dbres['unread_messages'] = count($get_all_mes);

        if ($user->id == $get_mess->sender_id) {

          $dbres['is_seen'] = 1;
        } else {

          $dbres['is_seen'] = $get_mess->receiver_seen;
        }

        $dbres['mess_last'] = $get_mess->body;

        $dbres['last_message_sent_time'] = $get_mess->created_at->format('Y-m-d H:i:s');
      } else {

        $dbres['mess_last'] = null;

        $dbres['is_seen'] = null;

        $dbres['last_message_sent_time'] = null;
      }

      if ($dbres['latest_active_at'] != null && $dbres['latest_active_at'] >= now()->subMinutes(10)) {

        $dbres['is_online'] = true;
      } else {

        $dbres['is_online'] = false;
      }
    }
    return $user_all;
  }

  public function sortArrayChat($user_id, $listUser)
  {
    $list_chat = [];
    $get_list_mess = Messages::where(function ($qr) use ($user_id) {
      $qr->where('sender_id', $user_id)
        ->orwhere('receiver_id', $user_id);
    })
      ->orderBy('created_at', 'DESC')
      ->get();
    foreach ($get_list_mess as $line) {
      if ($line->sender_id == $user_id) {
        $list_chat[] = $line->receiver_id;
      } else {
        $list_chat[] = $line->sender_id;
      }
    }
    // foreach($listUser as $user){
    //   if(!in_array($user,$list_chat)){
    //     $list_chat[] = $user;
    //   }
    // }
    return array_unique($list_chat);
  }

  public function getListTeacherForStudent($params = null)
  {

    $user = auth()->user();

    $teachers_course = $user->courses->pluck('teacher_id')->toArray();

    $users_message_status = $user->messagesSendedIsNotDelete->toArray();

    $new_users_message_status = [];

    foreach ($users_message_status as $user_message_status) {

      if ($user_message_status['lasted_message_seen_id'] > $user_message_status['delete_id_mess']) {

        array_push($new_users_message_status, $user_message_status['partner_id']);
      }
    }

    if (!empty($params['keyword'])) {

      $user_all = User::whereIn('id', $teachers_course)->where('id', '!=', $user->id)->withTrashed();

      $user_all = CommonBusiness::searchInCollection($user_all, $this->fieldSearchableListChat, $params['keyword']);
    } else {

      $ids_ordered = implode(',', $new_users_message_status);

      $user_all = User::whereIn('id', $new_users_message_status)->where('id', '!=', $user->id)->withTrashed();

      if (count($new_users_message_status) > 0) {

        $user_all = $user_all->orderByRaw("FIELD(id, $ids_ordered)");
      }
    }
    // dd($user_all->get());

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {

        $perpage = $params['perpage'];

        $user_all = $user_all->paginate($perpage);
      } else {
        $user_all = $user_all->paginate(PERPAGE);
      }
    } else {

      $user_all = $user_all->get();
    }

    foreach ($user_all as $dbres) {

      // dd($dbres);

      $user_message_status = UserMessageStatus::where('partner_id', '=', $dbres->id)->first();

      // dd($user_message_status);

      // dd($user_message_status->delete_id_mess);

      if ($user_message_status && $user_message_status->delete_id_mess != null) {

        $get_mess = $this->getListChatLast($user->id, $dbres->id);

        $get_all_mes = $this->model->newQuery()->where('id', '>', $user_message_status->delete_id_mess)->where('sender_id', '=', $dbres->id)->where('receiver_id', '=', $user->id)->where('receiver_seen', '=', 0)->get();
        if ($get_mess != NULL) {
          $dbres['unread_messages'] = count($get_all_mes);
          if ($user->id == $get_mess->sender_id) {
            $dbres['is_seen'] = 1;
          } else {
            $dbres['is_seen'] = $get_mess->receiver_seen;
          }
          $dbres['mess_last'] = $get_mess->body;
          $dbres['last_message_sent_time'] = $get_mess->created_at->format('Y-m-d H:i:s');
        } else {
          $dbres['mess_last'] = null;
          $dbres['last_message_sent_time'] = null;
          $dbres['is_seen'] = null;
        }
        if ($dbres['latest_active_at'] != null && $dbres['latest_active_at'] >= now()->subMinutes(10)) {
          $dbres['is_online'] = true;
        } else {
          $dbres['is_online'] = false;
        }
      } else {

        $get_mess = $this->getListChatLast($user->id, $dbres->id);

        $get_all_mes = $this->model->newQuery()->where('sender_id', '=', $dbres->id)->where('receiver_id', '=', $user->id)->where('receiver_seen', '=', 0)->get();
        if ($get_mess != NULL) {
          $dbres['unread_messages'] = count($get_all_mes);
          if ($user->id == $get_mess->sender_id) {
            $dbres['is_seen'] = 1;
          } else {
            $dbres['is_seen'] = $get_mess->receiver_seen;
          }
          $dbres['mess_last'] = $get_mess->body;
          $dbres['last_message_sent_time'] = $get_mess->created_at->format('Y-m-d H:i:s');
        } else {
          $dbres['mess_last'] = null;
          $dbres['last_message_sent_time'] = null;
          $dbres['is_seen'] = null;
        }
        if ($dbres['latest_active_at'] != null && $dbres['latest_active_at'] >= now()->subMinutes(10)) {
          $dbres['is_online'] = true;
        } else {
          $dbres['is_online'] = false;
        }
      }
    }
    return $user_all;
  }

  public static function getListChatLast($user_id, $receiver_id)
  {
    $latestMessage = Messages::where(function ($query) use ($user_id, $receiver_id) {
      $query = $query->where('sender_id', $receiver_id)->where('receiver_id', $user_id);
    })
      // ->OrWhere(function ($query) use ($user_id, $receiver_id) {
      //   $query = $query->where('sender_id', $receiver_id)->where('receiver_id', $user_id);
      // })
      // ->latest()
      ->orderBy('id', 'desc')
      ->first();
    return $latestMessage;
  }

  public static function checkContentDeleteMessenger($user_id, $receiver_id)
  {
    $get_last_delete = UserMessageStatus::where('user_id', $user_id)->where('partner_id', $receiver_id)->first();

    if (!$get_last_delete) {

      return false;
    }
    $get_mess = Messages::where(function ($query) use ($user_id, $receiver_id) {
      $query = $query->orwhere(function ($q) use ($user_id, $receiver_id) {
        $q->where('sender_id', $user_id)->where('receiver_id', $receiver_id);
      })
        ->orwhere(function ($q) use ($user_id, $receiver_id) {
          $q->where('sender_id', $receiver_id)->where('receiver_id', $user_id);
        });

      // $query = $query->where('sender_id', $user_id)->where('receiver_id', $receiver_id)
      //               ->Orwhere('sender_id', $receiver_id)->where('receiver_id', $user_id);
    });

    // dd($get_mess);

    if ($get_last_delete->delete_id_mess != null) {

      $get_mess = $get_mess->where('id', '>', $get_last_delete->delete_id_mess);
    }

    $get_mess = $get_mess->orderBy('created_at', 'DESC')->latest()->first();

    // dd($get_mess);

    if ($get_mess) {

      return $get_mess->id;
    }

    return false;
  }

  public function deleteMessengers($user_id, $receiver_id, $message_id)
  {
    $get_last_delete = UserMessageStatus::where('user_id', $user_id)
      ->where('partner_id', $receiver_id)
      ->first();
    $get_last_delete->delete_id_mess = $message_id;
    $get_last_delete->save();
    // $get_mess = Messages::where(function ($query) use ($user_id, $receiver_id) {
    //     $query = $query->where('sender_id', $user_id)->where('receiver_id', $receiver_id);
    //   })
    //     ->OrWhere(function ($query) use ($user_id, $receiver_id) {
    //       $query = $query->where('sender_id', $receiver_id)->where('receiver_id', $user_id);
    // });
  }

  public function receiverSeen($sender_id)
  {

    $user = auth()->user();

    $query = $this->model->newQuery()->where('sender_id', '=', $sender_id)->where('receiver_id', '=', $user->id)->where('receiver_seen', '=', 0)->update(["receiver_seen" => 1]);

    // event(new \App\Events\GetListEventChat($user->id));

  }
}

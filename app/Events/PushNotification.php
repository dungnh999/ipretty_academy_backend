<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushNotification
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $receiver_id;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct($receiver_id)
  {
    // dd($receiver_id);
    $this->receiver_id = $receiver_id;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new Channel('UserReceiver.' . $this->receiver_id);
  }

  public function broadcastAs()
  {
    return 'notification';
  }

  public function broadcastWith()
  {
    return [
      'id' => $this->receiver_id
    ];
  }
}

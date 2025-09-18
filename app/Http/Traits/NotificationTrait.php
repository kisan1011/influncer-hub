<?php

namespace App\Http\Traits;

use App\Facade\CustomFacade;
use App\Models\Channel;
use App\Models\Notification;
use App\Models\User;

trait NotificationTrait
{

  public function sendNotification($type,$title,$body,$sender_id,$receiver_id,$related_id="")
  {
    Notification::create([
      'type' => $type,
      'title' => $title,
      'body' => $body,
      'sender_id' => $sender_id,
      'receiver_id' => $receiver_id,
      'related_id' => $related_id
    ],
    [
      'date' => now()->format('d M,Y'),
      'time' => now()->format('g:i a'),
      'status'=>Notification::STATUS_UNREAD
    ]);
    return CustomFacade::successResponse("Create notification.");
  }
}

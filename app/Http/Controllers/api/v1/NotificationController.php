<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\NotificationIdCheckRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Exception;

class NotificationController extends Controller
{
  //  Get all notification
  public function notification()
  {
    try {
      $notifications = Notification::select('id','title', 'date', 'body', 'time', 'status', 'sender_id', 'related_id')
        ->with('sender')
        ->where('status',Notification::STATUS_UNREAD)
        ->where('receiver_id', auth()->user()->id)
        ->orderBy('created_at', 'DESC')
        ->get();
      $unreadCount =  Notification::where(['receiver_id' => auth()->user()->id, 'status' => Notification::STATUS_UNREAD])->count();
      return CustomFacade::successResponse("Notification fetch successfully.", ['notifications' => $notifications , 'unread_count' => $unreadCount]);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // All unread notification to change status read
  public function readAll(Request $request)
  {
    try {
      $notifications = Notification::select('id', 'status')->where([
        'receiver_id' => auth()->user()->id,
        'status' => Notification::STATUS_UNREAD,
      ])->update(['status' => Notification::STATUS_READ]);

      return CustomFacade::successResponse("All notification read.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Delete notification
  public function deleteNotification(NotificationIdCheckRequest $request)
  {
    try {
      $inquiry_ids = array_filter(array_unique(explode(',', $request->ids)));
      Notification::whereIn('id',$inquiry_ids)->delete();
      return CustomFacade::successResponse("Notification delete successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
}

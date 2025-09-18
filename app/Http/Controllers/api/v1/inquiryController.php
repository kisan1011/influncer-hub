<?php

namespace App\Http\Controllers\api\v1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use App\Models\Channel;
use App\Models\Inquiry;
use App\Mail\InquiryMail;
use App\Facade\CustomFacade;
use App\Models\Notification;
use App\Jobs\InquiryEmailJob;
use App\Models\InquiryMessage;
use App\Http\Traits\ImageTrait;
use App\Models\InquiryAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Traits\NotificationTrait;
use App\Http\Requests\api\v1\InquirySendRequest;
use App\Http\Requests\api\v1\InquiryTypeRequest;
use App\Http\Requests\api\v1\InquiryIdCheckRequest;
use App\Http\Requests\api\v1\InquiySendMessageRequest;

class inquiryController extends Controller
{
  use ImageTrait;
  use NotificationTrait;

  // Inquiry send
  public function inquirySend(InquirySendRequest $request)
  {
    try {

      $inquiryCheck = Inquiry::where('sender_id', auth()->user()->id)->where('channel_id', $request->channel_id)->first();
      if($inquiryCheck){
        throw new Exception("You have already opened inquiry for this channel.");
      }

      // Receiver details fetch
      $receiver = Channel::with('userPremission')->select('user_id', 'email', 'channel_name')->where('id', $request->channel_id)->first();

      // Store inquiry
      $inquiryData = [];
      $inquiryData['inquiry_id'] = $this->inquiryIdGenerate();
      $inquiryData['channel_id'] = $request->channel_id;
      $inquiryData['sender_id'] = auth()->user()->id;
      $inquiryData['receiver_id'] = $receiver->user_id;
      $inquiryData['subject'] = $request->subject;
      $inquiryData['link'] = $request->link;
      $inquiryStore = Inquiry::create($inquiryData);
      if(!$inquiryStore){
        throw new Exception("Something went wrong. Please try again.");
      }


     $messageStore = InquiryMessage::create([
        'inquiry_id' => $inquiryStore->id,
        'sender_id' => $inquiryStore->sender_id,
        'receiver_id' => $inquiryStore->receiver_id,
        'message' => $request->message,
        'link' => $request->link,
      ]);

      if($messageStore && $request->attachment){
        foreach ($request->attachment as $attachment) {
          $file = $this->inquiryImageUpload($attachment, 'storage/image/inquiry');
          InquiryAttachment::create(['inquiry_message_id' => $messageStore->id, 'attachment' => $file]);
        }
      }

      // Send mail
      if ($receiver->userPremission->email_notification == User::NOTIFICATION_ON) {
        $details = [
          'email' => $receiver->email,
          'channel_name' => $receiver->channel_name,
          'name' => $receiver->userPremission->name,
          'inquiry_user' => auth()->user()->name,
          'inquiry_id' => $inquiryStore->inquiry_id,
          'subject' => $request->subject,
          'message' => $request->message
        ];
          try {
            $email = new InquiryMail($details);
            Mail::to($details['email'])->send($email);
          } catch (\Throwable $th) {
            // dd($th->getMessage());
          }
        // dispatch(new InquiryEmailJob($details));
      }

      // Send notification
      $type = Notification::NOTIFICATION_INQUIRY_SEND;
      $text = auth()->user()->name . " send inquiry.";

      $this->sendNotification($type,$text,$messageStore->message,auth()->user()->id,$receiver->user_id,$inquiryStore->id);
      return CustomFacade::successResponse("Inquiry send successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Inquiry id generator
  public function inquiryIdGenerate()
  {
    $inquiry_id = Inquiry::latest()->value('inquiry_id');
    if ($inquiry_id != null) {
      $newInquiryId = sprintf("#%06d", (ltrim($inquiry_id, '#,0')) + 1);
    } else {
      $newInquiryId = "#000001";
    }
    return $newInquiryId;
  }

  // sendMessage
  public function sendMessage(InquiySendMessageRequest $request)
  {
    try {
      $inquiryId = Inquiry::select('id', 'inquiry_id', 'receiver_id', 'sender_id')->where('id', $request->id)->first();
      $userID = auth()->user()->id;
      $recvierId = ($userID == $inquiryId->sender_id)? $inquiryId->receiver_id : $inquiryId->sender_id;
      $inquiryMessageData = [];
      $inquiryMessageData['sender_id'] = $userID;
      $inquiryMessageData['receiver_id'] =$recvierId;
      $inquiryMessageData['inquiry_id'] = $inquiryId->id;
      $inquiryMessageData['link'] = $request->link;
      $inquiryMessageData['message'] = $request->message;
      $inquiryMessageData['created_at'] = now();
      $message = InquiryMessage::create($inquiryMessageData);
      if ($message && isset($request->attachment) && $request->attachment != null) {

        $attachmentFiles = $request->attachment;
        $attachmentFiles = collect($attachmentFiles)->map(function ($attachmentFile) {
          return $attachmentFile->getSize();
        })->toArray();
        $totalSizeofImages = array_sum($attachmentFiles);

        // $maxFileSize = 10 * 1024; // 10MB in kilobytes
        $maxFileSize = 10 * 1048576; // 10MB in kilobytes, 1048576 bytes = 1 MB

        if($totalSizeofImages > $maxFileSize) {
          return CustomFacade::errorResponse('File size exceeds the maximum limit (10MB).');
        }

        foreach ($request->attachment as $attachment) {
          // Validate the uploaded file size before attempting to upload it.

          if ($attachment->getSize() > $maxFileSize) {
            $message ='File size exceeds the maximum limit (10MB).';
            return CustomFacade::errorResponse($message);
          }
          $file = $this->inquiryImageUpload($attachment, 'storage/image/inquiry');
          InquiryAttachment::create(['inquiry_message_id' => $message->id, 'attachment' => $file]);
        }
      }

      $recvierUser = User::where('id', $recvierId)->first();
      if($recvierUser && $recvierUser->email_notification == User::NOTIFICATION_ON){
        $type = Notification::NOTIFICATION_INQUIRY_REPLIED;
        $text = auth()->user()->name . " replied your inquiry.";
        $body = $message->message;
        $this->sendNotification($type,$text,$body,$userID,$recvierId,$request->id);
      }

      return CustomFacade::successResponse("Message send successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
  // sendMessage
  public function messageList(InquiryIdCheckRequest $request)
  {
    try {
      $checkInquiry = Inquiry::select('id','sender_id','receiver_id')->find($request->id);
      if((!isset($checkInquiry->sender_id) || !isset($checkInquiry->receiver_id)) || ($checkInquiry->sender_id != auth()->user()->id &&  $checkInquiry->receiver_id != auth()->user()->id)){
        return CustomFacade::errorResponse("The inquiry id is invalid.");
      }
      $fetchNotification = Notification::where('receiver_id',auth()->user()->id)->where('related_id',$request->id)->where('status', Notification::STATUS_UNREAD)->get();
      if($fetchNotification){
        foreach ($fetchNotification as $key => $notification) {
          $notification->status = Notification::STATUS_READ;
          $notification->save();
        }
      }
      InquiryMessage::where(['inquiry_id'=>$request->id,'receiver_id'=>auth()->user()->id,'status'=>'0'])->update(['status'=>'1']);
      $inquiry = Inquiry::with(['channel:id,channel_name,image,published_at','messages','messages.senderUser:id,name,profile','messages.receiverUser:id,name,profile', 'messages.attachment:id,inquiry_message_id,attachment'])->where('id', $request->id)->first();
      $inquiry->date_time = $inquiry->created_at->format('d M,Y g:i A');
      collect($inquiry->messages)->map(function ($message) {
          $message->date_time = $message->created_at->format('d M,Y g:i A');
          $message->is_send_by_me = false;
          if($message->sender_id == auth()->user()->id){
            $message->is_send_by_me = true;
          }
      });
      $inquiry->profile = auth()->user()->profile;
      return CustomFacade::successResponse("Inquiry list fetch successfully.", $inquiry);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Show inquiry list
  public function inquiryList(InquiryTypeRequest $request)
  {
    try {

      $filed_name = auth()->user()->role_id == User::ROLE_BUSINESS ? 'sender_id' : 'receiver_id';
      $inquiryList = Inquiry::select('id','channel_id','sender_id','inquiry_id', 'subject', 'link', 'created_at')->with('channel:id,channel_name,type')->with('senderProfile:id,name')->withCount('unread')->with('lastMessage')

      ->when(isset($request->platform), function ($q) use ($request) {
          $type = $request->platform === 'youtube' ? 0 : 1;
          $q->whereHas('channel', function ($query) use ($type) {
              $query->where('type', $type);
          });
      })

      // Influencer filter
      ->when(auth()->user()->role_id == User::ROLE_INFLUENCER, function ($q) use($request) {
        if (in_array($request->filter, ['1','2','3','4','5','6','7'])) {

            if ($request->filter == '1' || $request->filter == '2') {
                // Last 7 or 15 days
                $days = ($request->filter == '1') ? 7 : 15;
                $start_date = now()->subDays($days)->startOfDay();
                $end_date   = now()->endOfDay();

            } elseif (in_array($request->filter, ['3','4','5'])) {
                // Last 1, 3, or 6 months
                $months = ($request->filter == '3') ? 1 : (($request->filter == '4') ? 3 : 6);
                $start_date = now()->subMonths($months)->startOfDay();
                $end_date   = now()->endOfDay();

            } elseif ($request->filter == '6') {
                // Last year (calendar year OR rolling year)
                $start_date = now()->subYear()->startOfYear();
                $end_date   = now()->subYear()->endOfYear();
                // OR if  meant rolling 365 days:
                // $start_date = now()->subYear()->startOfDay();
                // $end_date   = now()->endOfDay();

            } elseif ($request->filter == '7') {
                // Custom
                $start_date = Carbon::parse($request->start_date)->startOfDay();
                $end_date   = Carbon::parse($request->end_date)->endOfDay();
            }

            $q->whereBetween('created_at', [$start_date, $end_date]);
        }
    })


      ->when(!empty($request->search), function ($q) use ($request) {
          $q->where(function ($query) use ($request) {
              $query->where('inquiry_id', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('subject', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('channel', function ($channelQuery) use ($request) {
                        $channelQuery->where('channel_name', 'LIKE', '%' . $request->search . '%');
                    });
          });
      })
      ->where($filed_name, auth()->user()->id)
      ->orderBy('created_at', 'DESC')
      ->paginate(10);
      if($inquiryList->lastPage() < $request->page){
        throw new Exception("page not found.");
      }
      $inquiryList->getCollection()->transform(function ($list, $key) {
        $list->date = $list->created_at->format('d M,Y');
        $list->time = $list->created_at->format('g:i A');
        $list->lastMessage->last_activity = "Last activity on ".$list->lastMessage->created_at->format('d M,Y');
        unset($list->created_at);
        return $list;
      });
      return CustomFacade::successResponse("Inquiry list fetch successfully.", $inquiryList);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
}

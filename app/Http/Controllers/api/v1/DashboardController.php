<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\ChannelTrait;
use App\Models\Channel;
use App\Models\Inquiry;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  use ChannelTrait;

  public function index(Request $request)
  {
    try {

    $type = $request->type == 'youtube' ? 0 : 1;

    $data = array();
    $userId = auth()->user()->id;
    $data['inquirys'] = Inquiry::with(['senderProfile' => function ($query) {
                      $query->select('id', 'profile');
                    }])->latest()
                    ->whereHas('channel', function ($query) use ($type) {
                      $query->where('type', $type);
                    })
                    ->where('receiver_id', $userId)->take(4)->get();
    $data['channels'] = Channel::latest()->where('user_id', $userId)->where('type', $type)->take(4)->get();

    $data['subscriber_count'] = 0;
    $data['view_count'] = 0;
    $data['video_count'] = 0;
    $data['followers_count'] = 0;
    $data['follows_count'] = 0;
    $data['media_count'] = 0;

    $channels = Channel::latest()
            ->where('user_id', $userId)
            ->where('type', $type)
            ->get();

    foreach ($channels as $channel) {
      if ($channel->type == 0) { // YouTube
        if ($channel->is_last_update !== Carbon::now()->format('Y-m-d')) {
            $channelData = $this->getChannelDetails($channel->channel_id);
            if ($channelData == false) {
                throw new Exception("Youtube Channel not found.");
            }
            $channel->subscriber_count = $channelData['subscriber_count'];
            $channel->view_count = $channelData['view_count'];
            $channel->video_count = $channelData['video_count'];
            $channel->is_last_update = Carbon::now()->format('Y-m-d');
            $channel->save();
        }

        $data['subscriber_count'] += $channel->subscriber_count;
        $data['view_count'] += $channel->view_count;
        $data['video_count'] += $channel->video_count;
      } else {
        $data['followers_count'] += $channel->followers_count;
        $data['follows_count'] += $channel->follows_count;
        $data['media_count'] += $channel->media_count;
      }
    }

    collect($data['inquirys'])->map(function ($inquiry) {
      $inquiry->date = $inquiry->created_at->format('d M,Y');
      $inquiry->time = $inquiry->created_at->format('g:i A');
    });
    return CustomFacade::successResponse("Dashboard fetch successfully.", $data);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

}

<?php

namespace App\Http\Traits;

use App\Models\Channel;
use App\Models\User;
use Google\Service\YouTube;
use Google_Client;

trait ChannelTrait
{
  public $service;
  public function __construct(Google_Client $client)
  {
    $client->setDeveloperKey(env('GOOGLE_SERVER_KEY'));
    $this->service = new YouTube($client);
  }

  //  Get channel details by channel id
  public function getChannelDetails($channel_id)
  {
    $channelDetails = $this->service->channels->listChannels('id,snippet,statistics',['id'=>$channel_id]);
    if(!isset($channelDetails->items[0]->id)){
      return false;
    }
    $channelData['channel_id'] = $channelDetails->items[0]->id;
    $channelData['channel_name'] = $channelDetails->items[0]->snippet->title;
    $channelData['image'] = $channelDetails->items[0]->snippet->thumbnails->default->url;
    $channelData['video_count'] = $channelDetails->items[0]->statistics->videoCount;
    $channelData['view_count'] = $channelDetails->items[0]->statistics->viewCount;
    $channelData['subscriber_count'] = $channelDetails->items[0]->statistics->subscriberCount;
    $channelData['custom_url'] = $channelDetails->items[0]->snippet->customUrl;
    $channelData['published_at'] = date('Y-m-d', strtotime($channelDetails->items[0]->snippet->publishedAt));
    return $channelData;
  }

  // authorize user Statistics data update
  public function channelStatisticsUpdate($channel_ids)
  {
    $queryParams = [
      'id' =>$channel_ids->implode(','),
      'maxResults'=>$channel_ids->count()
    ];
    $response = $this->service->channels->listChannels('statistics',$queryParams);

    foreach ($response->items as $channel) {
      $channelData = [
        'view_count'=>$channel->statistics->viewCount,
        'video_count'=>$channel->statistics->videoCount,
        'subscriber_count'=>$channel->statistics->subscriberCount
      ];
      Channel::where('channel_id',$channel->id)->update($channelData);
    }
  }
}

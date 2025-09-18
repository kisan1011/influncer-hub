<?php

namespace App\Console\Commands;

use App\Models\Channel;
use Google\Service\YouTube;
use Google_Client;
use Illuminate\Console\Command;

class ChannelDataUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Channel statistics data update.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $fetchChannelIds = Channel::pluck('channel_id');
      if($fetchChannelIds->isNotEmpty()){
          $client = new Google_Client();
          $client->setDeveloperKey(env('GOOGLE_SERVER_KEY'));
          $service = new YouTube($client);
          $queryParams = [
              'id' =>$fetchChannelIds->implode(','),
              'maxResults'=>$fetchChannelIds->count()
          ];
          $response = $service->channels->listChannels('statistics',$queryParams);
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
}

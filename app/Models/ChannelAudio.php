<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelAudio extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'audio_id'
      ];
}

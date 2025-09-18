<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelCountry extends Model
{
    use HasFactory;
    protected $fillable = [
      'channel_id',
      'country_id'
    ];
}

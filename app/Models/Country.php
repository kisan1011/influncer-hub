<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  use HasFactory;
  protected $fillable = [
    'name', 'code'
  ];

  // Channel relation
  public function channel()
  {
    return $this->belongsToMany(Channel::class, 'channel_countries');
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
  use HasFactory;
  protected $guarded = ['id'];

  protected $fillable = [
    'name',
    'slug',
  ];

  // Channel relation
  public function channel()
  {
    return $this->belongsTo(Channel::class, 'audio_id', 'id');
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
  use HasFactory;
  protected $guarded = ['id'];

  protected $fillable = [
    'type',
    'email',
    'otp',
  ];

  const TYPE_VERIFICATION = '1';
  const TYPE_FORGOT_PASSWORD = '2';
}

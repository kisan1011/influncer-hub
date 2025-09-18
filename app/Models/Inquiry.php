<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
  use HasFactory;
  protected $fillable = [
    'inquiry_id',
    'channel_id',
    'sender_id',
    'receiver_id',
    'subject',
    'link',
    'created_at',
    'status',
  ];

  //Status
  const STATUS_READ = '1';
  const STATUS_UNREAD = '0';
  public static $status = [
    self::STATUS_READ => 'Read',
    self::STATUS_UNREAD => 'Unread',
  ];

  public function messages()
  {
    return $this->hasMany(InquiryMessage::class)->orderBy('created_at', 'DESC');
  }

  public function lastMessage()
  {
    return $this->hasOne(InquiryMessage::class)->latest();
  }

  public function unread()
  {
    return $this->hasMany(InquiryMessage::class)->where('receiver_id',auth()->user()->id)->where('status','0');
  }

  public function senderProfile()
  {
    return $this->hasOne(User::class,'id','sender_id');
  }

  public function channel()
  {
    return $this->belongsTo(Channel::class);
  }

}

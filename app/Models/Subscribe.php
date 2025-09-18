<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
  use HasFactory;
  protected $guarded = ['id'];

  protected $fillable = [
    'email',
    'status'
  ];

  // Status
  const STATUS_SUBSCRIBE = "1";
  const STATUS_UNSUBSCRIBE = "0";
  public static $status = [
    self::STATUS_SUBSCRIBE => 'Subscribe',
    self::STATUS_UNSUBSCRIBE => 'Unsubscribe',
  ];

  // Get status html
  public function getStatus()
  {
    $html = '---';
    if ($this->status == self::STATUS_SUBSCRIBE) {
      $html = '<span class="badge badge-success navbar-badge">' . Self::$status[self::STATUS_SUBSCRIBE] . '</span>';
    } elseif ($this->status == self::STATUS_UNSUBSCRIBE) {
      $html = '<span class="badge badge-danger navbar-badge">' . Self::$status[self::STATUS_UNSUBSCRIBE] . '</span>';
    }
    return $html;
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelContentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
      'channel_id',
      'content_cat_id'
    ];

    public function contentCategoryDetails()
    {
      return $this->hasMany(ContentCategory::class, 'id','content_cat_id')->select('id', 'name');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['post_id', 'message'];
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

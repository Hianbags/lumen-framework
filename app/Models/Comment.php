<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'user_id','post_id','comment_id'
    ];
    public $timestamps = false;

    public function post(){
        return $this->belongsTo(Post::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function parentComment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }
    
}

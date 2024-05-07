<?php

namespace App\Models;

use App\Policies\PostPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = "posts";
    protected $fillable = [
        'title',
        'description',
        'content',
        'author',
        'image',
        'view',
        'tag',
        'rating',
        'comment',
        'status',
        'user_id',
    ];
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_posts')->withPivot([]);
    }

}

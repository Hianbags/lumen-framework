<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post as ModelsPost;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {        
        $getReplies = function($comment) use (&$getReplies) {
            $replies = [];
            foreach ($comment->replies as $reply) {
                $replyData = [
                    'id' => $reply->id,
                    'user' => [
                        'username' => $reply->user->name,
                        'email' => $reply->user->email,
                    ],
                    'content' => $reply->content,
                ];
                if ($reply->replies->isNotEmpty()) {
                    $replyData['replies'] = $getReplies($reply);
                }
                $replies[] = $replyData;
            }
            return $replies;
        };
        
        $comments = Comment::where('post_id', $this->id)
                            ->whereNull('comment_id')
                            ->with('user')
                            ->with('replies.user') 
                            ->get();                     
        $commentData = $comments->map(function ($comment) use ($getReplies) {
            return [
                'id' => $comment->id,
                'user' => [
                    'username' => $comment->user->name,
                    'email' => $comment->user->email,
                ],
                'content' => $comment->content,
                'replies' => $getReplies($comment),
            ];
        });

        return [
            'data'=> [
                'id' => $this->id,
                'title' => $this->title,
                'content' => $this->content,
                'description'=> $this->description,
                'author' => DB::table('users')->where('id', $this->user_id)->value('name'),
                'categories'=> DB::table('categories_posts')->where('post_id', $this->id)
                    ->join('categories', 'categories_posts.category_id', '=', 'categories.id')
                    ->select('categories.id','categories.title')->get(),
                'view'=> $this->view,
                'tag'=> $this->tag,
                'rating' => DB::table('ratings')->where('post_id', $this->id)->avg('rating'),
                'comment' => $commentData,
                'created_at'   => $this->created_at->format('H:i:s m/d/Y'),
                'updated_at'=> $this->updated_at->format('H:i:s m/d/Y'),
            ],
        ];
    }
}

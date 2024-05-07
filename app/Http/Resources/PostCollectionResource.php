<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use PSpell\Config;

class PostCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
      public function toArray($request)
    {
        return [
            
            'data' => $this->collection->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'image' => Config('app.url').'/storage/'.$post->image,
                    'description' => $post->content,
                    'author' => DB::table('users')->where('id', $post->user_id)->value('name'),
                    'categories' => DB::table('categories_posts')->where('post_id', $post->id)
                    ->join('categories', 'categories_posts.category_id', '=', 'categories.id')
                    ->select('categories.id','categories.title')->get(),
                    'view' => $post->view,
                    'tag' => $post->tag,
                    'rating' => DB::table('ratings')->where('post_id', $post->id)->avg('rating'),
                    'created_at' => $post->created_at->format('H:i:s m/d/Y'),
                    'updated_at' => $post->updated_at->format('H:i:s m/d/Y'),
                ];
            }),
        ];
    }
    public function withResponse($request, $response)
    {
        $data = $response->getData(true);
        $data['meta'] = [
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'path' => $this->path(),
        ];

        $response->setData($data);
    }
    
}

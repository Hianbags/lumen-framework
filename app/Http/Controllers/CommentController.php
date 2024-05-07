<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{


    public function store(Request $request,$id)
    {
        $this->validate($request, [
        'content' => 'required',
         ]);
         try{
         $cmt = new Comment();
         $cmt->content = $request->content;
         $cmt->post_id = $id;
         $cmt->user_id = Auth::user()->id;
         if ($request->has('comment_id')) {
            $cmt->comment_id = $request->comment_id;
        }       
         $cmt->save();
        return response()->json(['status' => 'success', 'message' => 'Comment created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' =>'error','message'=> $e->getMessage()], 500);
        }
        
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'content' => 'required',
        ]);
        try{
        $comment = Comment::findOrFail($id);
        if ($comment->user_id != Auth::user()->id) {
            return response()->json(['error' => 'You are not authorized to update this comment.'], 403);
        }
        $comment->content = $request->content;
        $comment->save();
        return response()->json(['status' => 'success', 'message' => 'Comment updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' =>'error','message'=> $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            if ($comment->user_id != Auth::user()->id) {
                return response()->json(['status' =>'error', 'message'=> 'You are not authorized to delete this comment.'], 403);
            }
            $comment->delete();
            return response()->json(['status' => 'success', 'message' => 'Comment deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' =>'error','message'=> $e->getMessage()], 500);
        }
        
    }

}
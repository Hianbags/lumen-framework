<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollectionResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $limit = $request->input("limit", 5);
        $query = Post::query()->where('status', 1);
        if (!empty($input['title'])) {
            $query->where('title', 'like', '%' . $input['title'] . '%');
        }
        if (!empty($input['created_at'])) {
            $query->whereDate('created_at', $input['created_at']);
        }
        if (!empty($input['updated_at'])) {
            $query->whereDate('updated_at', $input['updated_at']);
        }
        return new PostCollectionResource($query->paginate($limit));
    }
    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->increment('view');
            return new PostResource($post);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function rejected()
    {
        $this->authorize('rejected', Post::class);
        $post = Post::all()
            ->where('status', 2);
        return response()->json(['data'=> $post]);
    }
    public function pending()
    {
        $this->authorize('pending', Post::class);
        $post = Post::all()
            ->where('status', 0);
        return response()->json(['data'=> $post]);
    }
    public function approved()
    {
        $this->authorize('approved', Post::class);
        $post = Post::all()
            ->where('status', 1);
        return response()->json(['data'=> $post]);
    }
    //danh sách bài post cho author
    public function approvedByAuthor()
    {
        $post = Post::all()
            ->where('status', 1)
            ->where('user_id', Auth::user()->id);
        return response()->json(['data'=> $post]);
    }
    public function pendingByAuthor()
    {
        $post = Post::all()
            ->where('status', 0)
            ->where('user_id', Auth::user()->id);
        return response()->json(['data'=> $post]);
    }
    public function rejectedByAuthor()
    {
        $post = Post::all()
            ->where('status', 2)
            ->where('user_id', Auth::user()->id);
        return response()->json(['data'=> $post]);
    }
    public function store(Request $request)
    {
        $this->authorize('create' , Post::class);
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'content' => 'required',
            'image' => 'required',
            'tag' => 'required',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            ]);
        try {
            $request->merge(['user_id' => Auth::user()->id]);
            $post = Post::create($request->all());
            if ($request->hasFile('image')) {
                $allowedfileExtension = ['pdf', 'jpg', 'png'];
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if ($check) {
                    $name = 'storage/'.time() . $file->getClientOriginalName();
                    $file->move('storage', $name);
                    $post->image = $name;
                }
            }
            $post->save();
            $post = Post::latest()->first();
            $post->categories()->attach($request->category_ids);
            return response()->json(['status' => 'success'  ,'message' => 'Post created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['status'=> 'error', 'message' => $e->getMessage()], 409);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            if($this->authorize('update', $post)){
                $this->validate($request, [
                    'title' => 'required',
                    'description' => 'required',
                    'content' => 'required',
                    'image' => 'required',
                    'tag' => 'required',
                    'category_ids' => 'required|array',
                    'category_ids.*' => 'exists:categories,id',
                ]);
                $post->update($request->all());
                if ($request->hasFile('image')) {
                    $allowedfileExtension = ['pdf', 'jpg', 'png'];
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $check = in_array($extension, $allowedfileExtension);
                    if ($check) {
                        $name = 'localhost:8000/storage/'.time() . $file->getClientOriginalName();
                        $file->move('storage', $name);
                        $post->image = $name;
                    }
                }
                $post->save();
                $post->categories()->sync($request->category_ids);
                return response()->json(['status' => 'success', 'message' => 'Post updated successfully!'], 200);
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException  $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 409);
        }
    }
    
    public function destroy($id)
    {
        try{
        $post = Post::findOrFail($id);
        if($this->authorize('delete', $post)){
            $post->delete();
            return response()->json(['status' => 'success', 'message' => 'Post deleted successfully!'], 200);
        }
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException  $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 409);
    }
    }
    public function approve($id)
    {
        try{
        $post = Post::findOrFail($id);
        if($this->authorize('approve', $post)){
            $post->status = 1;
            $post->save();
            return response()->json(['status' => 'success', 'message' => 'Post approved successfully!'], 200);
        }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException  $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 409);
        }
    }
    public function reject($id)
    {
        try{
        $post = Post::findOrFail($id);
        if($this->authorize('reject', $post)){
            $post->status = 2;
            $post->save();
            return response()->json(['status' => 'success', 'message' => 'Post rejected successfully!'], 200);
        }
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException  $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 409);
    }
    }
}

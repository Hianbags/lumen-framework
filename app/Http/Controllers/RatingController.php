<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    
    public function store(Request $request){
        try{
            if(Rating::where('user_id', auth()->id())->where('post_id', $request->post_id)->exists()){
                return response()->json(['status'=> 'error','message'=> 'Rating already exists' ], 400);
            }
            $this->validate($request, [
                'post_id' => 'required|exists:posts,id',
                'rating' => 'required|integer|between:1,5',
            ]);
            Rating::create([
                'user_id' => auth()->id(),
                'post_id' => $request->post_id,
                'rating' => $request->rating,
            ]);
            return response()->json(['status'=> 'success','message'=> 'Rating added successfully' ], 201);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
        } 
    }

}

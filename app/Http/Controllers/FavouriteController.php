<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function index(){
        $favourites = Favourite::where('user_id', auth()->id())->get();
        return response()->json(['data' => $favourites], 200);
    }
    public function store(Request $request){
       try{
        $this->validate($request, [
            'post_id' => 'required|exists:posts,id',
        ]);
        Favourite::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
        ]);
        return response()->json(['status'=> 'success','message'=> 'Favourite added successfully' ], 201);
       }catch(\Exception $e){
           return response()->json(['message' => $e->getMessage()], 400);
       } 
    }
    public function destroy($id){
        try{
            $favourite = Favourite::findOrFail($id)->where('user_id', auth()->id());
            if($favourite){
                $favourite->delete();
                return response()->json(['status'=> 'success','message'=> 'Favourite removed successfully' ], 200);
            }else{
                return response()->json(['status'=> 'error','message'=> 'Favourite not found' ], 404);
            }
        }
        catch(\Exception $e){
            return response()->json(['status'=> 'error','message' => $e->getMessage()], 400);
        }
    }

}

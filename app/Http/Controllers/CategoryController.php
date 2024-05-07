<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $categories = Category::all();
        try {
            if (!empty($input['title'])){    
                $categories = Category::where('title','like', '%' .$input['title']. '%')->get();
            }
            if (!empty($input['id'])){
                $categories = Category::where('id',$input['id'])->get();
            }
            return response()->json(['data' => $categories], 200);
        } catch (\Exception $e) {
            return response()->json(['status'=>'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        try {
            $category = Category::with('children')->find($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return response()->json(['data' => $category], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category_id' => 'nullable|exists:categories,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            Category::create($request->all());
            return response()->json(['status' => 'success' , 'message' => 'Category created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('update', $category);
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category_id' => 'nullable|exists:categories,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $category->update($request->all());

            return response()->json(['category' => $category, 'message' => 'Category updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('delete', $category);
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}

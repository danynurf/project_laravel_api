<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ProductCategory;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'message' => 'You do not have a permission to access the content.'
            ], 403);
        }

        $categories = Category::all();

        return response()->json([
            'data' => $categories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'message'=> 'You do not have a permission to access the content.'
            ], 403);
        }

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'description'=> 'required|string',
        ]);

        $result = Category::create($validated);

        if(!$result) {
            return response()->json([
                'message' => 'Failed to create category',
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully to created category',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'message' => 'You do not have a permission to access the content.'
            ], 403);
        }

        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'data'=> $category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'message' => 'You do not have a permission to access the content.'
            ], 403);
        }

        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $validated = $request->validate([
            'category'=> 'nullable|string|max:50',
            'description'=> 'nullable|string'
        ]);

        $result = $category->update($validated);

        if(!$result) {
            return response()->json([
                'message' =>'Failed to updated the category.'
            ], 500);
        }

        return response()->json([
            'message' =>'Successfully to updated the category.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'message' => 'You do not permission to access the content.'
            ], 403);
        }

        $category = Category::find($id);
        $categories = ProductCategory::where('category_id', $id);

        if(!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $categories->delete();
        $result = $category->delete();

        if(!$result) {
            return response()->json([
                'message' => 'Failed to delete product.'
            ], 500);
        }

        return response()->noContent();
    }
}

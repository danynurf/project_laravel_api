<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\DtlCart;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::select('id', 'name', 'img', 'price', 'stock');

        if(auth()->user()->role == 'seller') {
            $products = $products->where('seller_user_id', auth()->id());
        }

        $products = $products->get();

        return response()->json([
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role == 'buyer') {
            return response()->json([
                'message' => 'You are a buyer. You do not have a permission to add product',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:225',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'img' => 'required|string',
            'categories' => 'array|required',
            'categories.*.id' => 'required|integer'
        ]);

        if($validated['price'] < 0 || $validated['stock'] < 0) {
            return response()->json([
                'message' => 'The value must be higher than 0'
            ], 400);
        }

        $validated['seller_user_id'] = auth()->id();
        $categories = $validated['categories'];
        unset($validated['categories']);

        $isAny = Category::whereIn('id', $categories)->get();

        if(count($isAny) < count($categories)) {
            return response()->json([
                'message' => 'The category not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $product = Product::create($validated);

            foreach($categories as $category) {
                $data = [
                    'product_id' => $product->id,
                    'category_id' => $category['id'],
                ];

                ProductCategory::create($data);
            }

            DB::commit();
        } catch(\Throwable $th) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to add product'
            ], 500);
        }

        return response()->json([
            'message' => 'Product successfully added'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if(auth()->user()->role == 'seller') {
            $product = Product::where('seller_user_id', auth()->id())->find($id);
        }

        if(!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $categories = $product->categories;

        return response()->json([
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(auth()->user()->role == 'buyer') {
            return response()->json([
                'message' => 'You are buyer. You do not have a permission to access this product.'
            ], 403);
        }

        $product = Product::where('seller_user_id', auth()->id())->find($id);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|integer',
            'img' => 'nullable|string',
            'stock' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*.id' => 'nullable'
        ]);

        foreach($validated as $key => $value) {
            if($value == null || $value < 0) {
                $validated[$key] = $product[$key];
            }
        }

        if(isset($validated['categories'])) {
            $categories = $validated['categories'];
            unset($validated['categories']);

            $isAny = Category::whereIn('id', $categories)->get();

            if(count($isAny) < count($categories)) {
                return response()->json([
                    'message' => 'The category not found'
                ], 404);
            }

            $product_categories = ProductCategory::where('product_id', $id);
            $product_categories->delete();

            foreach($categories as $category) {
                $data = [
                    'product_id' => $id,
                    'category_id' => $category['id'],
                ];

                ProductCategory::create($data);
            }
        }

        $product = $product->update($validated);

        if(!$product) {
            return response()->json([
                'message'=> 'Failed to update product',
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully to update product'
        ], 200);
    }

    public function addStock(Request $request, string $id)
    {
        if(auth()->user()->role == 'buyer') {
            return response()->json([
                'message' => 'You are a buyer. You do not have a permission to access the product'
            ], 403);
        }

        $product = Product::where('seller_user_id', auth()->id())->find($id);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validate([
            'stock' => 'required|integer',
        ]);

        if($validated['stock'] < 0) {
            return response()->json([
                'message' => 'The stock must be higher than 0'
            ], 400);
        }

        $product->update([
            'stock' => $product->stock + $validated['stock'],
        ]);

        return response()->json([
            'message' => 'Successfully to update product'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(auth()->user()->role == 'buyer') {
            return response()->json([
                'message' => 'You are a buyer. You do not have a permission to access the product'
            ], 403);
        }

        $product = Product::find($id);
        $product_categories = ProductCategory::where('product_id', $id);
        $carts = DtlCart::where('product_id', $id);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        $carts->delete();
        $product_categories->delete();
        $product = $product->delete();

        if(!$product) {
            return response()->json([
                'message'=> 'Failed to update product',
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully to update product'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DtlCart;
use App\Models\HdrCart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private $hdr_cart;

    public function __construct() {
        $this->hdr_cart =  HdrCart::where('buyer_user_id', auth()->id())->first();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You are a seller. You do not have a permission to access the content.'
            ], 403);
        }

        $carts = DB::table('dtl_carts')
                    ->join('products', 'products.id', '=', 'dtl_carts.product_id')
                    ->select('products.id', 'products.name', 'products.img', 'products.description', 'products.price','dtl_carts.quantity')
                    ->where('hdr_cart_id', $this->hdr_cart->id)
                    ->get();

        return response()->json([
            'data' => $carts,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You are a seller. You do not have a permission to access the content.'
            ], 403);
        }

        $validated = $request->validate([
            'product_id'=> 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $product = Product::find($validated['product_id']);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $hdr_cart = HdrCart::where('buyer_user_id', auth()->id())->first();
        $cart = DtlCart::where('hdr_cart_id', $this->hdr_cart->id)->where('product_id', $validated['product_id'])->first();

        if($cart) {
            $quantity = $cart->quantity + $validated['quantity'];

            if($product->stock < $quantity) {
                $quantity = $product->stock;
            }

            $cart->update([
                'quantity' => $quantity,
            ]);

            return response()->json([
                'message' => 'Product already in cart and the quantity is updated.'
            ], 200);
        }

        if($product->stock < $validated['quantity']) {
            $validated['quantity'] = $product->stock;
        }

        $validated['hdr_cart_id'] = $this->hdr_cart->id;
        $result = DtlCart::create($validated);

        if(!$result) {
            return response()->json([
                'message' => 'Failed to add product to cart.'
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully to add product to cart.'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You are a seller. You do not have a permission to access the content.'
            ], 403);
        }

        $hdr_cart = HdrCart::where('buyer_user_id', auth()->id());
        $cart = DtlCart::where('hdr_cart_id', $this->hdr_cart->id)->find($id);

        if(!$cart) {
            return response()->json([
                'message' => 'Cart not found.'
            ], 404);
        }

        $product = $cart->product;

        return response()->json([
            'data' => $cart,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You are a seller. You do not have a permission to access the content.'
            ], 403);
        }

        $cart = DtlCart::find($id);

        if(!$cart) {
            return response()->json([
                'message' => 'Cart not found'
            ], 404);
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer',
        ]);

        if(isset($validated['quantity'])) {
            if($validated['quantity'] < 0) {
                return response()->json([
                    'message' => 'Quantity must be higher than 0.'
                ], 400);
            }
        }

        $result = $cart->update($validated);

        if(!$result) {
            return response()->json([
                'message' => 'Failed to update cart'
            ], 500);
        }

        return response()->json([
            'message' => 'Update cart successfully.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You are a seller. You do not have a permission to access the content.'
            ], 403);
        }

        $cart = DtlCart::find($id);

        if(!$cart) {
            return response()->json([
                'message' => 'Cart not found.'
            ], 404);
        }

        $cart->delete();

        return response()->noContent();
    }
}

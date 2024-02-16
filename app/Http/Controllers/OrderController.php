<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HdrOrder;
use App\Models\DtlOrder;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You do not have a permission to access the content.'
            ], 403);
        }

        $orders = HdrOrder::where('buyer_user_id', auth()->id())
                            ->select('id', 'total_price', 'payment', 'created_at as order_date')
                            ->get();

        return response()->json([
            'data' => $orders,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role != 'buyer') {
            return response()->json([
                'message' => 'You do not have a permission to access the content.'
            ], 403);
        }

        $validated = $request->validate([
            'products' => 'array|required',
            'products.*.id' => 'required|integer',
            'products.*.quantity' => 'required|integer',
        ]);

        $products = $validated['products'];

        foreach($products as $product) {
            $product = Product::find($product['id']);

            if(!$product) {
                return response()->json([
                    'message' => 'Product not found.'
                ], 404);
            }

            if($product->stock == 0) {
                return response()->json([
                    'message' => 'Product is out of stock.'
                ], 400);
            }
        }

        DB::beginTransaction();

        try {
            $total_price = 0;
            $hdr_order = HdrOrder::create([
                'buyer_user_id' => auth()->id(),
                'payment' => 0,
                'total_price' => 0,
            ]);

            foreach($validated['products'] as $prod) {
                $product = Product::find($prod['id']);

                if($product->stock < $prod['quantity']) {
                    $prod['quantity'] = $product->stock;
                }

                $stock = $product->stock;
                $product->update([
                    'stock' => $stock - $prod['quantity']
                ]);
                $subtotal = $prod['quantity'] * $product->price;
                $total_price += $subtotal;

                DtlOrder::create([
                    'product_id' => $prod['id'],
                    'quantity' => $prod['quantity'],
                    'subtotal' => $subtotal,
                    'hdr_order_id' => $hdr_order->id,
                ]);

                $cart = Cart::where('product_id', $prod['id'])
                            ->where('buyer_user_id', auth()->id())
                            ->first();

                if($cart) {
                    $cart->delete();
                }
            }

            $hdr_order->update([
                'total_price' => $total_price,
                'payment' => $total_price,
            ]);

            DB::commit();
        } catch(\Throwable $th) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to order.'
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully to checkout'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(auth()->user()->role == 'seller') {
            return response()->json([
                'message' => 'You do not have a permission to access the content.'
            ], 403);
        }

        $order = HdrOrder::select('id', 'total_price', 'created_at as order_date')
                        ->where('buyer_user_id', auth()->id())
                        ->find($id);
        $order_items = $order->items;

        if(!$order) {
            return response()->json([
                'message' => 'The order history not found'
            ], 404);
        }

        return response()->json([
            'data' => $order
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

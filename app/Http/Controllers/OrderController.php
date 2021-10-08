<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class OrderController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $product = Product::find($request->product_id);

        if(!$product) {
            return response(['message' => 'Product is not available.']);
        }

        if($product->available_stock < $request->quantity) {
            return response(['message' => 'Failed to order this product due to unavailability of the stock.'], 400);
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
        ]);

        if($order) {
            $product->available_stock = ($product->available_stock - $request->quantity);
            $product->save();

            return response(['message' => 'You have successfully ordered this product.'], 201);
        }

        return response(['message' => 'Something went wrong, try again later.']);

    }

}

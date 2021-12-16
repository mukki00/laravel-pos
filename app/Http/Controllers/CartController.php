<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class CartController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response(
                $request->user()->cart()->get()
            );
        }
        return view('cart.index');
    }

    public function store(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();
        $quantity = 0;
        if($product != null && $product->count() > 0){
            $quantity = $product->quantity;
        }
        $request->validate([
            'barcode' => 'required|exists:products,barcode',
        ]);
        $barcode = $request->barcode;

        $cart = $request->user()->cart()->where('barcode', $barcode)->first();
        if ($cart) {
            // update only quantity
            if($cart->pivot->quantity < $quantity){
            $cart->pivot->quantity = $cart->pivot->quantity + 1;
            }
            $cart->pivot->save();
        } else {
            $product = Product::where('barcode', $barcode)->first();
            $request->user()->cart()->attach($product->id, ['quantity' => 1]);
        }

        return response('', 204);
    }

    public function changeQty(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();
        $quantity = 0;
        if($product != null && $product->count() > 0){
            $quantity = $product->quantity;
        }
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:'.$quantity,
        ]);

        $cart = $request->user()->cart()->where('id', $request->product_id)->first();

        if ($cart) {
            $cart->pivot->quantity = $request->quantity;
            $cart->pivot->save();
        }

        return response([
            'success' => true
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);
        $request->user()->cart()->detach($request->product_id);

        return response('', 204);
    }

    public function empty(Request $request)
    {
        $request->user()->cart()->detach();

        return response('', 204);
    }
}

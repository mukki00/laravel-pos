<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = new Order();
        if($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items', 'payments', 'customer'])->latest()->paginate(10);

        $total = $orders->map(function($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function edit(Order $order)
    {
        return view('orders.edit')->with('order', $order);
    }

    public function store(OrderStoreRequest $request)
    {
        $cart = $request->user()->cart()->get();
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user()->id,
        ]);
        $total = 0;
        foreach ($cart as $item) {
            $order->items()->create([
                'price' => $item->price,
                'quantity' => $item->pivot->quantity,
                'product_id' => $item->id,
            ]);
            $total += $item->price * $item->pivot->quantity;
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }
        $request->user()->cart()->detach();
        $order->payments()->create([
            'amount' => $request->amount,
            'total' => $total,
            'user_id' => $request->user()->id,
        ]);
        return 'success';
    }
    public function update(OrderUpdateRequest $request,Order $order)
    {
        $updated = $order->payments()->first()->update([
            'amount' => $request->amount,
        ]);
        if (!$updated) {
            return redirect()->back()->with('error', 'Sorry, there\'re a problem while updating order.');
        }
        return redirect()->route('orders.index')->with('success', 'Success, your product have been updated.');
    }
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'success' => true
        ]);
    }
}

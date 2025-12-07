<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;




class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user = auth()->user();

        // Get cart items
        $cartItems = CartItem::where('user_id',$user->id)->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error'=>'Cart is empty'], 422);
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'error' => "Product {$item->product->name} does not have enough stock"
                ], 422);
            }
        }

        // All good -> create order within DB transaction
        $order = null;
        DB::transaction(function() use ($user, $cartItems, $request, &$order) {
            $total = 0;
            $order = Order::create([
               'user_id' => $user->id,
               'order_number' => 'ORD-' . time() . '-' . rand(100,999),
               'address' => $request->address,
               'phone' => $request->phone,
               'total' => 0
            ]);

            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->find($item->product->id);
                // decrease stock
                $product->stock -= $item->quantity;
                if ($product->stock <= 0) {
                    $product->stock = 0;
                }
                $product->save();
                // only three decimal places

                $itemTotal = $item->quantity * $product->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $product->price,
                    'total_price' => round($itemTotal, 4)
                ]);

                $total += $itemTotal;
            }

            $order->total = $total;
            $order->save();

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();
        });

        // return summary
        $order->load('items.product');

        return response()->json([
            'order_number' => $order->order_number,
            'total' => floatval($order->total),
            'items' => $order->items->map(function($it){
                return [
                    'product_id' => $it->product_id,
                    'product_name' => $it->product->name,
                    'quantity' => $it->quantity,
                    'unit_price' => floatval($it->unit_price),
                    'total_price' => floatval($it->total_price)
                ];
            })
        ], 201);
    }

    public function index()
    {
        $orders = auth()->user()->orders()->with('items.product')->get();
        return response()->json($orders);
    }

    public function show($orderId)
    {
        $order = auth()->user()->orders()->with('items.product')->findOrFail($orderId);
        return response()->json($order);
    }
}

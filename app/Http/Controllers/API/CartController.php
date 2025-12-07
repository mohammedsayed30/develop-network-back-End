<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cartItems()->with('product')->get();
        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        $user = auth()->user();
        $product = Product::find($data['product_id']);
        if ($product->stock < $data['quantity']) {
            return response()->json(['error'=>'Not enough stock'], 422);
        }
        $cartItem = CartItem::updateOrCreate(
            ['user_id'=>$user->id, 'product_id'=>$product->id],
            ['quantity' => DB::raw("GREATEST(1, LEAST({$data['quantity']}, {$product->stock}))")]
        );
        $cartItem = $cartItem->fresh();
        return response()->json($cartItem, 201);
    }

    public function remove($id)
    {
        $item = CartItem::where('user_id', auth()->id())->where('id',$id)->firstOrFail();
        $item->delete();
        return response()->json(['message'=>'Removed']);
    }
}

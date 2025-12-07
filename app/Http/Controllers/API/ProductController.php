<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $product->update($data);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

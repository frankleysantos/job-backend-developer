<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductServices;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $product;

    public function __construct(ProductServices $service) {
        $this->middleware('auth:api', ['except' => ['apiFakeStore']]);
        $this->product = $service;
    }

    public function store(ProductRequest $productRequest) 
    {
        $product = $this->product->store($productRequest); 
        return response()->json($product);
    }

    public function update(ProductRequest $productRequest) 
    {
        $product = $this->product->update($productRequest);      
        return response()->json($product);
    }

    public function delete($id)
    {
        $message = $this->product->delete($id);    
        return response()->json($message);
    }

    public function show(Request $request)
    {
        if ($request->id || $request->name) {
            $response = $this->product->getEntity($request->id, $request->name);
        } else {
            $response = $this->product->getAll();
        }
        return response()->json($response);
    }

    public function apiFakeStore($product_id = null)
    {
        $products = $this->product->apiFakeStore($product_id);
        return response()->json($products);
    }
}
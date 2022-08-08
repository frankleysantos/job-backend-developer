<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Core\BaseEloquentRepository;
use Illuminate\Support\Facades\Http;


class EloquentProductRepository extends BaseEloquentRepository implements ProductRepositoryInterface
{
    public function entity() 
    {
        return Product::class;
    }

    public function fakeStore($product_id)
    {
        $baseUrl = "https://fakestoreapi.com/products/";
        $product_id = (int) $product_id;
        if ($product_id) 
            $baseUrl =  $baseUrl . "{$product_id}";

        $response = Http::get($baseUrl);
        $json = $response->json();
        return $json;
    }
}
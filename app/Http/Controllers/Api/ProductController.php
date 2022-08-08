<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $product;

    public function __construct(ProductRepositoryInterface $productInterface) {
        $this->middleware('auth:api', ['except' => ['apiFakeStore']]);
        $this->product = $productInterface;
    }

    public function store(ProductRequest $productRequest) 
    {
        $file = $productRequest->file('image_url');
        $productData = $productRequest->all();
        if ($file) {
            $filename = time().$file->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                '/image/products/',
                $file,
                $filename
            );
            $productData['image_url'] =$filename;
        }
        $product = $this->product->store($productData);
        $message = "Produto cadastrado com sucesso";
        if( !isset($product->id))
            $message = 'Erro ao cadastrar produto.';
        return response()->json($message);
    }

    public function update(ProductRequest $productRequest) 
    {
        $file = $productRequest->file('image_url');
        $productData = $productRequest->all();
        $productExists = $this->product->getEntity($productData['id']);
        if (isset($productExists)) 
            Storage::disk('public')->delete("/image/products/".$productExists['image_url']);
        
        if ($file) {
            $filename = time().$file->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                '/image/products/',
                $file,
                $filename
            );
            $productData['image_url'] =$filename;
        }
        $productData = new Request($productData);
        $product = $this->product->update($productData);
        $message = "Produto atualizado com sucesso";
        if( $product == 0)
            $message = 'Erro ao atualizar produto.';
        return response()->json($message);
    }

    public function delete($id)
    {
        $productExists = $this->product->getEntity($id);
        if (isset($productExists)) {
            Storage::disk('public')->delete("/image/products/".$productExists['image_url']);
            $response = $this->product->delete($id);
            $message = "Produdo deletado com sucesso.";
        } else {
            $message = 'Produto não existe';
        }
            
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
        $products = $this->product->fakeStore($product_id);
        $productsCreated = collect();
        if ( $products) {
            $product = $products;
            if ($product_id) {
                $formatProducts = $this->formatProductsApi($product);
                if ($prod = $this->product->getEntity(null, $product['title'])) {
                    return response()->json(['Produto ja esta cadastrado.', 'error' => $prod]);
                }  
                $this->product->store($formatProducts);
                return response()->json('Produto cadastrado com sucesso');       
            }
            
            foreach ($products as $product) {
                $formatProducts = $this->formatProductsApi($product);
                if ($prod = $this->product->getEntity(null, $product['title'])) {
                    $productsCreated->push($prod);
                } else {
                    $this->product->store($formatProducts);
                } 
            }
            return response()->json([
                'Produtos cadastrados com sucesso',
                'error' => $productsCreated
            ]);
        }
    }

    public function formatProductsApi($product) : array 
    {
        return [
            'name' => $product['title'],
            'price' => $product['price'],
            'description' => $product['description'],
            'category' => $product['category'],
            'image_url' => $product['image'],
        ];
    }
}
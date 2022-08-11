<?php
namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductServices
{
    private $repo;

    public function __construct(ProductRepositoryInterface $repo)
    {
       $this->repo = $repo; 
    }
    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function getEntity($id = null, $name = null)
    {
        return $this->repo->getEntity($id, $name);
    }

    public function store($request)
    {
        $file = $request->file('image_url');
        $productData = $request->all();
        if ($file) {
            $filename = time().$file->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                '/image/products/',
                $file,
                $filename
            );
            $productData['image_url'] =$filename;
        }
        $product = $this->repo->store($productData);
        $message = "Produto cadastrado com sucesso";
        if( !isset($product->id))
            $message = 'Erro ao cadastrar produto.';
        return $message;
    }

	public function update($request)
    {
        $file = $request->file('image_url');
        $productData = $request->all();
        $productExists = $this->repo->getEntity((int)$productData['id']);
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
        $product = $this->repo->update($productData);
        $message = "Produto atualizado com sucesso";
        if( $product == 0)
            $message = 'Erro ao atualizar produto.';
        return $message;
    }

	public function delete($id)
    {
        $productExists = $this->repo->getEntity($id);
        if (isset($productExists)) {
            Storage::disk('public')->delete("/image/products/".$productExists['image_url']);
            $this->repo->delete($id);
            $message = "Produdo deletado com sucesso.";
        } else {
            $message = 'Produto não existe';
        }
        return $message;
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

    public function apiFakeStore($product_id = null)
    {
        $products = $this->repo->fakeStore($product_id);
        $productsCreated = collect();
        if ( $products) {
            $product = $products;
            if ($product_id) {
                $formatProducts = $this->formatProductsApi($product);
                if ($prod = $this->repo->getEntity(null, $product['title'])) {
                    return ['Produto ja esta cadastrado.', 'error' => $prod];
                }  
                $this->repo->store($formatProducts);
                return 'Produto cadastrado com sucesso';       
            }
            
            foreach ($products as $product) {
                $formatProducts = $this->formatProductsApi($product);
                if ($prod = $this->repo->getEntity(null, $product['title'])) {
                    $productsCreated->push($prod);
                } else {
                    $this->repo->store($formatProducts);
                } 
            }
            return [
                'Produtos cadastrados com sucesso',
                'error' => $productsCreated
            ];
        }
    }
}
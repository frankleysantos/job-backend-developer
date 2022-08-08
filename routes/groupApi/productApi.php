<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth/product'
], function ($router) {
    Route::post('/store', [ProductController::class, 'store']);
    Route::post('/update', [ProductController::class, 'update']);
    Route::get('/delete/{id}', [ProductController::class, 'delete']); 
    Route::post('/show', [ProductController::class, 'show']); 
    Route::post('/fakeStore/{id?}', [ProductController::class, 'apiFakeStore'])->name('products.fake'); 
});
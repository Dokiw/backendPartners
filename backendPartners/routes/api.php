<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ProductController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



//создание
Route::post('/catalog', [CatalogController::class, 'store']);

// Маршрут для получения списка товаров с возможностью указания limit и fields
Route::get('/catalogs', [CatalogController::class, 'index']);

// Маршрут для получения конкретного товара по ID
Route::get('/catalogs/{id}', [CatalogController::class, 'show']);


//создание
Route::post('/product', [ProductController::class, 'store']);

// Маршрут для получения списка товаров с возможностью указания limit и fields
Route::get('/products', [ProductController::class, 'index']);

// Обновление товара (PUT или PATCH)
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::patch('/products/{id}', [ProductController::class, 'update']);

// Маршрут для получения конкретного товара по ID
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::put('/catalogs/{id}', [CatalogController::class, 'update']);

Route::patch('/catalogs/{id}', [CatalogController::class, 'update']);

Route::delete('/catalogs/{id}', [CatalogController::class, 'destroy']);

Route::get('/catalogss', [CatalogController::class, 'getBySubName']);

Route::get('/productss', [ProductController::class, 'IndexForWeb']);

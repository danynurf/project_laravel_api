<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/register/{role}', [AuthController::class, 'register'])
    ->name('register');


Route::middleware(['auth:api'])->group(function () {

    Route::post('/registering/{role}', [AuthController::class, 'register'])
        ->name('register.seller');


    // Product route

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('/products/{id}', [ProductController::class, 'show'])
        ->name('products.show');

    Route::put('/products/{id}', [ProductController::class, 'update'])
        ->name('products.update');

    Route::put('/products/{id}/stock', [ProductController::class, 'addStock'])
        ->name('products.stock');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
        ->name('products.destroy');


    // Category Route

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('categories.store');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/{id}', [CategoryController::class, 'show'])
        ->name('categories.show');

    Route::put('/categories/{id}', [CategoryController::class, 'update'])
        ->name('categories.update');

    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');


    // Cart route

    Route::post('carts', [CartController::class, 'store'])
        ->name('carts.store');

    Route::get('/carts', [CartController::class, 'index'])
        ->name('carts.index');

    Route::get('/carts/{id}', [CartController::class, 'show'])
        ->name('carts.show');

    Route::put('carts/{id}', [CartController::class, 'update'])
        ->name('carts.update');

    Route::delete('carts/{id}', [CartController::class, 'destroy'])
        ->name('carts.destroy');
});


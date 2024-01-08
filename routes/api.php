<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Public routes
// Route::get('/products/search/{name}', [ProductController::class, 'search']);
// Route::get('/view_all_category/', [CategoryController::class, 'viewAllCategory']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/category', [ProductController::class, 'viewCategory']);
Route::get('/products/{id}', [ProductController::class, 'show']);
// Route::get('/products/{id}', s[ProductController::class, 'show']);
Route::get('/all_category', [CategoryController::class, 'viewCategory']);
Route::get('/products/products/{id}', [ProductController::class, 'publicShow']);
Route::get('/products/relatedProducts/{id}', [ProductController::class, 'relatedProducts']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'Login_in']);

Route::get('/view-category', [CategoryController::class, 'viewCategory']);
Route::get('/category', [CategoryController::class, 'viewAllCategory']);


// Route::resource('products', ProductController::class);
Route::get('/order', [OrderController::class, 'index']);
Route::get('/orders', [ProductController::class, 'orderForLoggedInUsers']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/all_orders', [ProductController::class, 'allOrdersFOrAdmin']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/add_cart', [CartController::class, 'store']);
    Route::post('/cart_qty_update/{cart_id}/{scope}', [CartController::class, 'updateQuantity']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::delete('/cart_del/{id}', [CartController::class, 'destroy']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/validate-order', [OrderController::class, 'validateOrder']);
    Route::get('/edit-category/{id}', [CategoryController::class, 'getSingleCategory']);
    Route::put('/update-category/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/delete_category/{id}', [CategoryController::class, 'deleteCategory']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/category/{slug}', [ProductController::class, 'fetchSlug']);
    Route::get('/admin/products', [ProductController::class, 'index']);
    Route::post('/store-category', [CategoryController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
});

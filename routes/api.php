<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Models\ProductInventory;
use App\Models\Size;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('/', [AuthController::class, 'redirectToAuth']);
    Route::get('callback', [AuthController::class, 'handleAuthCallback']);
});

// Route::get('auth', [AuthController::class, 'redirectToAuth']);
// Route::get('auth/callback', [AuthController::class, 'handleAuthCallback']);


Route::prefix('categories')->group(function () {
    Route::get('/', [CategoriesController::class, "index"]);
    Route::get('/{id}', [CategoriesController::class, "getCategoryById"]);
    Route::post('/', [CategoriesController::class, "addCategory"]);
    Route::put('/{id}', [CategoriesController::class, 'updateCategory']);
    Route::delete('/{id}', [CategoriesController::class, "deleteCategory"]);
    Route::get('/search/{search}', [CategoriesController::class, 'search']);
});


Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, "index"]);
    Route::get('/{id}', [ProductController::class, "getProductById"]);
    Route::post('/', [ProductController::class, "addProduct"]);
    Route::patch('/{id}', [ProductController::class, 'updateProduct']);
    Route::put('/{id}', [ProductController::class, "deleteProduct"]);
    Route::get('/search/{search}', [ProductController::class, 'search']);
});

Route::prefix('productInventory')->group(function () {
    Route::get('/{id}', [ProductInventoryController::class, "index"]);
    Route::get('/{productId}/{inventoryId}', [ProductInventoryController::class, "getInventoryById"]);
    Route::post('/', [ProductInventoryController::class, "add"]);
    Route::put('/{productId}/{inventoryId}', [ProductInventoryController::class, 'update']);
    Route::delete('/{id}', [ProductInventoryController::class, "delete"]);
});

Route::prefix('images')->group(function () {
    Route::get('/{id}', [ImageController::class, "index"]);
    Route::post('/', [ImageController::class, "add"]);
    Route::put('/{productId}/{inventoryId}', [ImageController::class, 'update']);
    Route::delete('/{id}', [ImageController::class, "delete"]);
});

Route::prefix('colors')->group(function () {
    Route::get('', [ColorController::class, "index"]);
    Route::get('/{id}', [ColorController::class, "getColorById"]);
    Route::post('/', [CategoriesController::class, "addCategory"]);
    Route::put('/{id}', [CategoriesController::class, 'updateCategory']);
    Route::delete('/{id}', [CategoriesController::class, "deleteCategory"]);
});

Route::prefix('coupons')->group(function () {
    Route::get('/', [CouponController::class, "index"]);
    Route::get('/{id}', [CouponController::class, "getCouponById"]);
    Route::post('/', [CouponController::class, "addCoupon"]);
    Route::put('/{id}', [CouponController::class, 'updateCoupon']);
    Route::delete('/{id}', [CouponController::class, "deleteCoupon"]);
    Route::get('/search/{search}', [CouponController::class, 'search']);
});


Route::prefix('sizes')->group(function () {
    Route::get('/', [SizeController::class, "index"]);
    Route::get('/{id}', [ColorController::class, "getColorById"]);
    Route::post('/', [CategoriesController::class, "addCategory"]);
    Route::put('/{id}', [CategoriesController::class, 'updateCategory']);
    Route::delete('/{id}', [CategoriesController::class, "deleteCategory"]);
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, "index"]);
    // Route::get('/{id}', [ColorController::class, "getColorById"]);
    Route::post('/', [CartController::class, "addToCart"]);
    Route::put('/{id}', [CartController::class, 'update_cart']);
    Route::delete('/{id}', [CartController::class, "delete"]);
});

Route::prefix('checkout')->group(function () {
    Route::post('/momo', [OrderController::class, "momo_payment"]);
});

Route::prefix('order')->group(function () {
    Route::get('', [OrderController::class, "get_order"]);
    Route::get('/admin', [OrderController::class, "get_all_orders"]);
    Route::post('/add', [OrderController::class, "add_order"]);
    Route::put('/{code}', [OrderController::class, "updateOrder"]);
});

Route::prefix('order_details')->group(function () {
    Route::get('/{code}', [OrderController::class, "get_order_details"]);
});

Route::prefix('wishlist')->group(function () {
    Route::post('/', [WishlistController::class, "add"]);
});

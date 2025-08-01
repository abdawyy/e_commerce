<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GuestUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\categoriesController;
use App\Http\Controllers\discountCodesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\userController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/legal', function () {
    return view('legal');
})->name('legal');


Route::get('/lang/{lang}', function ($lang) {
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en'; // default fallback
    }
    session()->put('locale', $lang);
    app()->setLocale($lang);
    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
    return view('index');
});
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/send-contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('product/show/{id}', [ProductController::class, 'productWebShow'])->name('product.show');
Route::match(['get', 'post'], 'product/list/category/{id?}', [ProductController::class, 'productWebList'])->name('product.List');


Route::get('admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminController::class, 'login']);
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');



// Protect admin dashboard and other routes
Route::middleware(['admin'])->group(function () {

    //admin routes

    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/list', [AdminController::class, 'list'])->name('admin.list');
    Route::get('admin/list/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');


    Route::get('admin/register', [AdminController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('admin/register', [AdminController::class, 'register']);
    Route::get('/admin/status/{id}', [AdminController::class, 'toggleUserStatus'])->name('admin.toggleStatus');

    //types routes
    Route::match(['get', 'post'], 'admin/type/edit/{id?}', [TypeController::class, 'edit'])->name('type.edit');
    Route::get('admin/type/list', [TypeController::class, 'list'])->name('type.list');
    Route::get('admin/type/delete/{id}', [TypeController::class, 'delete'])->name('type.delete');
    Route::get('/admin/type/status/{id}', [TypeController::class, 'toggleUserStatus'])->name('admin.type.toggleStatus');

    // categories routes
    Route::match(['get', 'post'], 'admin/categories/edit/{id?}', [categoriesController::class, 'edit'])->name('categories.edit');
    Route::get('admin/categories/list', [categoriesController::class, 'list'])->name('categories.list');
    Route::get('admin/categories/delete/{id}', [categoriesController::class, 'delete'])->name('categories.delete');
    Route::get('/admin/categories/status/{id}', [categoriesController::class, 'toggleUserStatus'])->name('admin.categories.toggleStatus');

    // discount codes routes
    Route::match(['get', 'post'], 'admin/discountCodes/edit/{id?}', [discountCodesController::class, 'edit'])->name('discountCodes.edit');
    Route::get('admin/discountCodes/list', [discountCodesController::class, 'list'])->name('discountCodes.list');
    Route::get('admin/discountCodes/delete/{id}', [discountCodesController::class, 'delete'])->name('discountCodes.delete');
    Route::get('/admin/discountCodes/status/{id}', [discountCodesController::class, 'toggleUserStatus'])->name('admin.discountCodes.toggleStatus');


    // product routes
    Route::match(['get', 'post'], 'admin/products/edit/{id?}', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('admin/products/list', [ProductController::class, 'list'])->name('products.list');
    Route::get('admin/products/delete/{id}', [ProductController::class, 'delete'])->name('products.delete');
    Route::get('admin/products/image/delete/{id}', [ProductController::class, 'deleteImage'])->name('image.delete');
    Route::get('/admin/products/status/{id}', [ProductController::class, 'toggleUserStatus'])->name('admin.cities.toggleStatus');



    // cities routes
    Route::match(['get', 'post'], 'admin/cities/edit/{id?}', [CitiesController::class, 'edit'])->name('cities.edit');
    Route::get('admin/cities/list', [CitiesController::class, 'list'])->name('cities.list');
    Route::get('/admin/cities/status/{id}', [CitiesController::class, 'toggleUserStatus'])->name('admin.cities.toggleStatus');

    //user
    Route::get('admin/users/list', [userController::class, 'list'])->name('users.list');

    //orders
    Route::get('admin/order/list', [AdminController::class, 'orderList'])->name('order.list');
    // Show order details
    Route::get('/admin/order/edit/{id}', [AdminController::class, 'orderShow'])->name('order.show');
    // Update order status
    Route::put('admin/order/{id}/status', [AdminController::class, 'changeOrderStatus'])->name('order.changeStatus');

    Route::get('/admin/user/edit/{id}', [AdminController::class, 'userShow'])->name('user.show');
    Route::get('/admin/user/status/{id}', [UserController::class, 'toggleUserStatus'])->name('admin.users.toggleStatus');



    Route::get('/admin/contact/list', [ContactController::class, 'list'])->name('admin.contact.list');

    Route::get('/admin/reviews/{productId}', [ReviewController::class, 'list'])->name('admin.reviews.byProduct');
    Route::get('/admin/reviews/status/{id}', [ReviewController::class, 'toggleUserStatus'])->name('admin.users.toggleStatus');



    Route::get('admin/guest/list', [GuestUserController::class, 'list'])->name('admin.guest.list');
    Route::get('/admin/guest/edit/{id}', [GuestUserController::class, 'guestShow'])->name('admin.guest.show');
















    // Other admin routes...
});
// Route::get('/admin/dashboard', function () {
//     return view('admin.index');
// });







Route::match(['get', 'post'], '/cart/add', [CartController::class, 'add']);
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/delete/{id}', [CartController::class, 'delete'])->name('cart.delete');
Route::get('/checkout/address', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/order', [CheckoutController::class, 'order'])->name('checkout.order');
Route::get('/cart/delete/guest/{key}', [CartController::class, 'deleteGuest'])->name('cart.guest.delete');


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'active.user'])->group(function () {

    // cart routes & checkout routes

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

});

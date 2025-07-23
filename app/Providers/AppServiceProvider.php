<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Type;
use App\Models\products;
use Illuminate\Support\Facades\Auth; // For accessing user info if needed
use App\Models\shoppingCart;
use App\Models\Cities;
use Illuminate\Support\Facades\Lang;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share categories and types with all views
        // Share categories and types with all views
        View::composer('*', function ($view) {
            $categories = Category::where('is_active', 1)->get();
            $types = Type::where('is_active', 1)->get();
      $products = products::with([
        'productImages',
        'productItems',
        'orderItems',
        'category',
        'type'
    ])
    ->where('is_active', 1)
    ->whereHas('category', function ($query) {
        $query->where('is_active', 1);
    })
    ->whereHas('type', function ($query) {
        $query->where('is_active', 1);
    })
    ->get();


            $cartCount = 0;

            // Check if the user is authenticated and retrieve cart data
            if (Auth::check()) {
                $userId = Auth::id();
                // Count items in the shopping cart for the authenticated user
                $cartCount = ShoppingCart::where('user_id', $userId)->count();
            }
$cities = Cities::where('is_active', 1)->get();


            // Share the cart count with all views

            $view->with(compact('categories', 'types', 'products', 'cartCount', 'cities'));
            Lang::addNamespace('web', resource_path('lang/' . app()->getLocale() . '/web'));

        });
    }
}

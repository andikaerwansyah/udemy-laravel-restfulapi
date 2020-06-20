<?php

namespace App\Providers;

use App\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // automatically change the status of product if the qty equals to 0 
        Product::updated(function($product){
            if ($product->quantity == 0 && $product->isAavailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;

                $product->save();
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

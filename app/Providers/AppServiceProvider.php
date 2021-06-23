<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserEmailChanged;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::created(function ($user) {
            Mail::to($user)->send(new UserCreated($user));
        });
        User::updated(function ($user) {
            if ($user->isDirty('email')) {

                Mail::to($user)->send(new UserEmailChanged($user));
            }
        });
        Product::updated(function ($product) {
            if ($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });
    }
}

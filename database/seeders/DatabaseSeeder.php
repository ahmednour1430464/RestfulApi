<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        //deleting all data from tables
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();
        
        $user_quantities=1000;
        $category_quantities=30;
        $product_quantities=1000;
        $transaction_quantities=1000;

        User::factory($user_quantities)->create();
        Category::factory($category_quantities)->create();
        Product::factory($product_quantities)->create()->each(
            function($product){
                $categories=Category::all()->random(mt_rand(1,5))->pluck('id');
                $product->categories()->attach($categories);
            }
        );
        Transaction::factory($transaction_quantities)->create();

        Schema::enableForeignKeyConstraints();
    }
}

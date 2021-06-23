<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:'. ProductTransformer::class)->only(['store']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];
        $this->validate($request, $rules);

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('the buyer must be different from the seller.', 409);
        }
        if (!$buyer->isVerified()) {
            return $this->errorResponse('buyer must be verified user.', 409);
        }
        if (!$product->seller->isVerified()) {
            return $this->errorResponse('seller must be verified user.', 409);
        }
        if (!$product->isAvailable()) {
            return $this->errorResponse('the specified product is not available.', 409);
        }
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('the specified product doest not have enough units for this transaction.', 409);
        }

        return DB::transaction(function ()use($request,$product,$buyer) {
            $product->quantity-=$request->quantity;
            $product->save();

            $transaction=Transaction::create([
                'quantity'=>$request->quantity,
                'product_id'=>$product->id,
                'buyer_id'=>$buyer->id,
            ]);
            return $this->showOne($transaction);
        });
    }
}

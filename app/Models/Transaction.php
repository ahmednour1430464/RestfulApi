<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\Buyer;
use App\Transformers\TransactionTransformer;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $transformer=TransactionTransformer::class;
    protected $table='transactions';
    protected $fillable=[
        'quantity',
        'buyer_id',
        'product_id',
    ];

    public function buyer(){
       return $this->belongsTo(Buyer::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}

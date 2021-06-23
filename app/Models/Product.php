<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\Category;
use App\Transformers\ProductTransformer;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    const AVAILABLE_PRODUCT='available';
    const UNAVAILABLE_PRODUCT='unavailable';
    public $transformer=ProductTransformer::class;
    protected $table = 'products';
    protected $fillable=[
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];
    protected $hidden=[
        'pivot'
    ];

    public function isAvailable(){
        return $this->status== Product::AVAILABLE_PRODUCT;
    }

    public function seller(){
        return $this->belongsTo(Seller::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Transformers\CategoryTransformer;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $transformer=CategoryTransformer::class;
    protected $table='categories';
    protected $fillable=[
        'name',
        'description'
    ];
    protected $hidden=[
        'pivot'
    ];

    public function products(){
        return $this->belongsToMany(Product::class);
    }
}

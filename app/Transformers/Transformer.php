<?php
namespace App\Transformers;

interface Transformer{
    public static function originalAttribute($index);
    public static function TransformedAttribute($index);
}
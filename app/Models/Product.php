<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $keyType = 'string';
    protected $guarded = ['product_id'];

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function productCategory() {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}

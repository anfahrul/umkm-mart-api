<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'merchants';
    protected $primaryKey = 'merchant_id';
    protected $keyType = 'string';
    protected $guarded = ['merchant_id'];

    public function productCategory() {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function products() {
        return $this->hasMany(Product::class, 'merchant_id');
    }
}

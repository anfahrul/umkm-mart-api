<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function merchants() {
        return $this->hasMany(Merchant::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}

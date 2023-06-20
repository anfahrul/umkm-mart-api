<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductImage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

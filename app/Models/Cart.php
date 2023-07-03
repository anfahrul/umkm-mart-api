<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cart extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_id',
        'merchant_id',
        'total',
    ];

    public $timestamps = false;

    public function cart() {
        return $this->belongsTo(Customer::class);
    }

    public function cartDetails() {
        return $this->hasMany(CartDetail::class, 'cart_id');
    }
}

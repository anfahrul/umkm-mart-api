<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CartDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cart_details';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function cart() {
        return $this->belongsTo(Cart::class);
    }
}

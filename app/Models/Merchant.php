<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Merchant extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'merchants';
    protected $primaryKey = 'merchant_id';
    protected $keyType = 'string';
    protected $guarded = ['merchant_id'];

    public function umkmCategory() {
        return $this->belongsTo(UmkmCategory::class, 'umkm_category_id');
    }

    public function products() {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}

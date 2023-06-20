<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmCategory extends Model
{
    use HasFactory;

    protected $table = 'umkm_categories';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function merchants() {
        return $this->hasMany(Merchant::class);
    }
}

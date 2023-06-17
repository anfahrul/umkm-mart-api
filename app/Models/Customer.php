<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}

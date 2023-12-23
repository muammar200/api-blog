<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailUser extends Model
{
    use HasFactory;

    // protected $table = 'detail_users';
    protected $fillable = [
        'user_id',
        'firstname',
        'lastname'
    ];

    // public function detailUser(): HasOne
    // {
    //     return $this->hasOne(User::class);
    // }
}


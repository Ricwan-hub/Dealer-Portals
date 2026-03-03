<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DealerProfile extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function user() 
    { 
        return $this->morphOne(User::class, 'profile');
    }

    protected $attributes = [
        'address_country' => 'Kenya',
    ];
}

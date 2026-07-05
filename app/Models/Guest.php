<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = [
        'customer_code', 'prefix', 'first_name', 'last_name', 
        'birthdate', 'nationality', 'email', 'country_code', 'phone', 'guest_type'
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

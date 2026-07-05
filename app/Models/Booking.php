<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    protected $fillable = [
        'guest_id', 'booking_type', 'check_in', 'check_out', 
        'num_nights', 'base_price', 'grand_total', 'status'
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'booking_amenities')
                    ->withPivot('quantity', 'captured_price')
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripStop extends Model
{
    protected $fillable = [
        'trip_id',
        'sort_order',
        'place_id',
        'place_name',
        'latitude',
        'longitude',
        'checkin',
        'checkout',
        'nights',
        'hotel_id',
        'hotel_name',
        'hotel_photo',
        'offer_id',
        'hotel_price',
        'hotel_currency',
        'prebook_id',
        'transaction_id',
        'secret_key',
        'booking_id',
        'booking_status',
        'pricing',
    ];

    protected function casts(): array
    {
        return [
            'checkin' => 'date',
            'checkout' => 'date',
            'hotel_price' => 'decimal:2',
            'pricing' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prebook extends Model
{
    protected $fillable = [
        'prebook_id',
        'transaction_id',
        'secret_key',
        'hotel_id',
        'offer_id',
        'checkin',
        'checkout',
        'guests_adults',
        'holder_first_name',
        'holder_last_name',
        'holder_email',
        'status',
        'pricing',
        'booking_id',
    ];

    protected $casts = [
        'pricing' => 'array',
        'checkin' => 'date',
        'checkout' => 'date',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'session_id',
        'name',
        'adults',
        'status',
        'total_price',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

    public function stops(): HasMany
    {
        return $this->hasMany(TripStop::class)->orderBy('sort_order');
    }

    public function recalculateDates(): void
    {
        $stops = $this->stops()->get();
        if ($stops->isEmpty()) return;

        $currentDate = $stops->first()->checkin;
        if (!$currentDate) return;

        foreach ($stops as $stop) {
            $stop->checkin = $currentDate;
            $stop->checkout = $currentDate->copy()->addDays($stop->nights);
            $stop->save();
            $currentDate = $stop->checkout;
        }
    }

    public function recalculateTotal(): void
    {
        $this->total_price = $this->stops()->whereNotNull('hotel_price')->sum('hotel_price');
        $this->save();
    }
}

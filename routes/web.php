<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TripBuilderController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => 'nl|de|fr|es'],
], function () {
    // Home & Search
    Route::get('/', [SearchController::class, 'home'])->name('home');
    Route::get('/api/places', [SearchController::class, 'places'])->name('places');
    Route::post('/search', [SearchController::class, 'search'])->name('search');

    // Hotel detail
    Route::get('/hotels/{hotelId}', [HotelController::class, 'show'])->name('hotel.show');

    // Booking flow
    Route::post('/prebook', [BookingController::class, 'prebook'])->name('prebook');
    Route::get('/checkout/{prebookId}', [BookingController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{prebookId}/guest', [BookingController::class, 'saveGuest'])->name('checkout.guest');
    Route::get('/booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');

    // Trip Builder
    Route::get('/trip-builder', [TripBuilderController::class, 'index'])->name('trip-builder.index');
    Route::post('/trip-builder/create', [TripBuilderController::class, 'create'])->name('trip-builder.create');
    Route::get('/trip-builder/{trip}', [TripBuilderController::class, 'show'])->name('trip-builder.show');
    Route::post('/trip-builder/{trip}/stops', [TripBuilderController::class, 'addStop'])->name('trip-builder.add-stop');
    Route::put('/trip-builder/{trip}/stops/{stop}', [TripBuilderController::class, 'updateStop'])->name('trip-builder.update-stop');
    Route::delete('/trip-builder/{trip}/stops/{stop}', [TripBuilderController::class, 'removeStop'])->name('trip-builder.remove-stop');
    Route::post('/trip-builder/{trip}/reorder', [TripBuilderController::class, 'reorderStops'])->name('trip-builder.reorder');
    Route::get('/trip-builder/{trip}/stops/{stop}/hotels', [TripBuilderController::class, 'searchHotels'])->name('trip-builder.search-hotels');
    Route::get('/trip-builder/{trip}/stops/{stop}/hotels/{hotelId}', [TripBuilderController::class, 'hotelDetail'])->name('trip-builder.hotel-detail');
    Route::post('/trip-builder/{trip}/stops/{stop}/select', [TripBuilderController::class, 'selectHotel'])->name('trip-builder.select-hotel');
    Route::post('/trip-builder/{trip}/checkout', [TripBuilderController::class, 'checkout'])->name('trip-builder.checkout');
    Route::get('/trip-builder/{trip}/checkout', [TripBuilderController::class, 'checkoutPage'])->name('trip-builder.checkout-page');
    Route::post('/trip-builder/{trip}/guest', [TripBuilderController::class, 'saveGuest'])->name('trip-builder.save-guest');
    Route::get('/trip-builder/{trip}/confirm', [TripBuilderController::class, 'confirm'])->name('trip-builder.confirm');
});

<?php

use App\Http\Controllers\Api\TicketTierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('ticket-tiers', TicketTierController::class);

    // Single-action style route, sits outside the resourceful set.
    Route::patch('ticket-tiers/{ticketTier}/publish', [TicketTierController::class, 'publish'])
        ->name('ticket-tiers.publish');
});

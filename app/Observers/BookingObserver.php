<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Sync room status when booking is created
        if ($booking->room) {
            $booking->room->syncStatus();
        }
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Sync room status when booking status changes
        if ($booking->isDirty('status') && $booking->room) {
            $booking->room->syncStatus();
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        // Sync room status when booking is deleted
        if ($booking->room) {
            $booking->room->syncStatus();
        }
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        // Sync room status when booking is restored
        if ($booking->room) {
            $booking->room->syncStatus();
        }
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        // Sync room status when booking is force deleted
        if ($booking->room) {
            $booking->room->syncStatus();
        }
    }
}

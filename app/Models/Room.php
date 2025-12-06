<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'price',
        'description',
        'facilities',
        'status',
        'capacity',
        'original_capacity',
        'area',
        'images',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Get bookings for this room
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get bills for this room
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get complaints for this room
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Check if room is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if room is occupied
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Check if room is under maintenance
     */
    public function isMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Sync room status based on active bookings
     * Call this method to ensure room status is always accurate
     * This method is automatically called by BookingObserver when booking status changes
     */
    public function syncStatus(): bool
    {
        // Force reload from database to get fresh data
        $this->refresh();
        
        // Get fresh bookings count directly from database
        $occupiedBookingsCount = \DB::table('bookings')
            ->where('room_id', $this->id)
            ->where('status', 'occupied')
            ->count();
        
        $pendingBookingsCount = \DB::table('bookings')
            ->where('room_id', $this->id)
            ->where('status', 'pending')
            ->count();
        
        $confirmedBookingsCount = \DB::table('bookings')
            ->where('room_id', $this->id)
            ->where('status', 'confirmed')
            ->count();

        $oldStatus = $this->status;
        $updates = [];

        // If there's an occupied booking, room MUST be occupied
        if ($occupiedBookingsCount > 0) {
            if ($this->status !== 'occupied') {
                $updates['status'] = 'occupied';
                // Store original capacity if not already stored
                $currentCapacity = \DB::table('rooms')->where('id', $this->id)->value('capacity');
                $currentOriginalCapacity = \DB::table('rooms')->where('id', $this->id)->value('original_capacity');
                if (!$currentOriginalCapacity) {
                    $updates['original_capacity'] = $currentCapacity;
                }
                $updates['capacity'] = 1;
            }
        } else {
            // No occupied booking
            if ($this->status === 'occupied') {
                // Check if there are any pending/confirmed bookings
                if ($pendingBookingsCount == 0 && $confirmedBookingsCount == 0) {
                    // No active bookings, room becomes available
                    $updates['status'] = 'available';
                    // Restore original capacity
                    $currentOriginalCapacity = \DB::table('rooms')->where('id', $this->id)->value('original_capacity');
                    $updates['capacity'] = $currentOriginalCapacity ?? 1;
                    $updates['original_capacity'] = null;
                }
            }
        }

        // Only update if there are changes
        if (!empty($updates)) {
            // Use query builder to avoid triggering model events
            \DB::table('rooms')->where('id', $this->id)->update($updates);
            // Refresh model attributes
            $this->refresh();
            return true;
        }

        return false;
    }
}

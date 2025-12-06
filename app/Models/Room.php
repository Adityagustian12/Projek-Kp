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
        // Reload bookings to get fresh data
        $this->load('bookings');
        
        $hasOccupiedBooking = $this->bookings()
            ->where('status', 'occupied')
            ->exists();

        $oldStatus = $this->status;
        $updates = [];

        if ($hasOccupiedBooking) {
            // If there's an occupied booking, room must be occupied
            if ($this->status !== 'occupied') {
                $updates['status'] = 'occupied';
                // Store original capacity if not already stored
                if (!$this->original_capacity) {
                    $updates['original_capacity'] = $this->capacity;
                }
                $updates['capacity'] = 1;
            }
        } else {
            // No occupied booking
            if ($this->status === 'occupied') {
                // Check if there are any pending/confirmed bookings
                $hasActiveBooking = $this->bookings()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->exists();
                
                if (!$hasActiveBooking) {
                    // No active bookings, room becomes available
                    $updates['status'] = 'available';
                    // Restore original capacity
                    $updates['capacity'] = $this->original_capacity ?? 1;
                    $updates['original_capacity'] = null;
                }
            }
        }

        // Only update if there are changes
        if (!empty($updates)) {
            // Use query builder to avoid triggering model events
            static::where('id', $this->id)->update($updates);
            // Refresh model attributes
            $this->refresh();
            return true;
        }

        return false;
    }
}

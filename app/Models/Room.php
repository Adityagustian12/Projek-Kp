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
     * Get the first image URL for the room
     */
    public function getFirstImageUrlAttribute()
    {
        if (!$this->images || count($this->images) === 0) {
            return null;
        }

        return $this->getImageUrl($this->images[0]);
    }

    /**
     * Get image URL - tries multiple methods for reliability
     */
    public function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        // Normalize path
        $imagePath = ltrim($imagePath, '/');
        
        // Try Storage::url() first (most reliable)
        try {
            if (\Storage::disk('public')->exists($imagePath)) {
                return \Storage::disk('public')->url($imagePath);
            }
        } catch (\Exception $e) {
            // Fallback to asset()
        }

        // Fallback to storage_url() helper
        return storage_url($imagePath);
    }

    /**
     * Get all image URLs for the room
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images || count($this->images) === 0) {
            return [];
        }

        return array_map(function($imagePath) {
            return $this->getImageUrl($imagePath);
        }, $this->images);
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
        // Get fresh bookings count directly from database (bypass any caching)
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

        // Get current room data directly from database
        $currentRoom = \DB::table('rooms')->where('id', $this->id)->first();
        if (!$currentRoom) {
            return false; // Room doesn't exist
        }
        
        $currentStatus = $currentRoom->status ?? 'available';
        $currentCapacity = $currentRoom->capacity ?? 1;
        $currentOriginalCapacity = $currentRoom->original_capacity ?? null;

        $updates = [];

        // If there's an occupied booking, room MUST be occupied
        if ($occupiedBookingsCount > 0) {
            // Always update to occupied if there's an occupied booking
            if ($currentStatus !== 'occupied') {
                $updates['status'] = 'occupied';
                // Store original capacity if not already stored
                if (!$currentOriginalCapacity && $currentCapacity > 0) {
                    $updates['original_capacity'] = $currentCapacity;
                }
                $updates['capacity'] = 1;
            }
        } else {
            // No occupied booking
            if ($currentStatus === 'occupied') {
                // Check if there are any pending/confirmed bookings
                if ($pendingBookingsCount == 0 && $confirmedBookingsCount == 0) {
                    // No active bookings, room becomes available
                    $updates['status'] = 'available';
                    // Restore original capacity
                    $updates['capacity'] = $currentOriginalCapacity ?? 1;
                    $updates['original_capacity'] = null;
                }
            }
        }

        // Only update if there are changes
        if (!empty($updates)) {
            // Use query builder to directly update database
            $updated = \DB::table('rooms')->where('id', $this->id)->update($updates);
            // Refresh model attributes
            $this->refresh();
            return $updated > 0;
        }

        return false;
    }
}

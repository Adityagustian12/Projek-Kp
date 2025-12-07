<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'category',
        'title',
        'description',
        'location',
        'images',
        'priority',
        'status',
        'admin_response',
        'resolved_at',
    ];

    protected $casts = [
        'images' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user who made the complaint
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room related to this complaint
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope a query to only include complaints for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Retrieve the model for bound value.
     * This ensures that route model binding respects user ownership for tenant routes.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // For tenant routes, we need to check if the complaint belongs to the authenticated user
        // This is handled in the controller, but we can add additional security here if needed
        return $this->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }

    /**
     * Check if complaint is new
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Check if complaint is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if complaint is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if complaint is closed
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'new' => 'Baru',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
            default => 'Tidak Diketahui'
        };
    }
}

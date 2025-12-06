<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'profile_picture',
        'birth_date',
        'gender',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'id_card_number',
        'id_card_file',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is tenant
     */
    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    /**
     * Check if user is seeker
     */
    public function isSeeker(): bool
    {
        return $this->role === 'seeker';
    }

    /**
     * Get user's bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get user's bills
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get user's complaints
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Change user role from seeker to tenant
     */
    public function becomeTenant(): bool
    {
        if ($this->role === 'seeker') {
            $this->update(['role' => 'tenant']);
            return true;
        }
        return false;
    }

    /**
     * Check if user can become tenant
     */
    public function canBecomeTenant(): bool
    {
        return $this->role === 'seeker' && $this->hasConfirmedBooking();
    }

    /**
     * Check if user has confirmed booking
     */
    public function hasConfirmedBooking(): bool
    {
        return $this->bookings()->where('status', 'confirmed')->exists();
    }

    /**
     * Check if user currently has an active (occupied) booking
     */
    public function hasActiveBooking(): bool
    {
        return $this->bookings()->where('status', 'occupied')->exists();
    }

    /**
     * Get user's payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get user's current occupied room
     */
    public function getCurrentRoom()
    {
        $occupiedBooking = $this->bookings()
                               ->where('status', 'occupied')
                               ->with('room')
                               ->first();
        
        return $occupiedBooking ? $occupiedBooking->room : null;
    }
}

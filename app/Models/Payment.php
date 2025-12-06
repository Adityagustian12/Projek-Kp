<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'bill_id',
        'amount',
        'payment_method',
        'payment_proof',
        'status',
        'verified_by',
        'verified_at',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user who made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bill for this payment
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get the admin who verified this payment
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if payment is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Diverifikasi',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabel(): string
    {
        return match($this->payment_method) {
            'bank_transfer' => 'Transfer Bank',
            'dana' => 'DANA',
            'gopay' => 'GoPay',
            'cash' => 'Tunai',
            'e_wallet' => 'E-Wallet',
            'other' => 'Lainnya',
            default => 'Tidak Diketahui'
        };
    }
}

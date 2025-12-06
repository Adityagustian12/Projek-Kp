<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, get existing data before changing column type
        $bookings = \Illuminate\Support\Facades\DB::table('bookings')
            ->whereNotNull('payment_proof')
            ->select('id', 'payment_proof', 'dp_amount', 'created_at')
            ->get();
        
        // Change column type to TEXT first (MySQL can't directly change string to json)
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('payment_proof')->nullable()->change();
        });
        
        // Migrate existing data: convert string to array
        foreach ($bookings as $booking) {
            $proof = $booking->payment_proof;
            // Check if it's already JSON array
            $decoded = json_decode($proof, true);
            if (!is_array($decoded)) {
                // Convert single string to array format
                $createdAt = $booking->created_at;
                if (is_string($createdAt)) {
                    $createdAt = \Carbon\Carbon::parse($createdAt)->format('Y-m-d H:i:s');
                } elseif ($createdAt instanceof \DateTime) {
                    $createdAt = $createdAt->format('Y-m-d H:i:s');
                } else {
                    $createdAt = now()->format('Y-m-d H:i:s');
                }
                
                \Illuminate\Support\Facades\DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'payment_proof' => json_encode([[
                            'path' => $proof,
                            'amount' => floatval($booking->dp_amount ?? 0),
                            'created_at' => $createdAt,
                        ]])
                    ]);
            }
        }
        
        // Now change to JSON type
        Schema::table('bookings', function (Blueprint $table) {
            $table->json('payment_proof')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, get existing data before changing column type
        $bookings = \Illuminate\Support\Facades\DB::table('bookings')
            ->whereNotNull('payment_proof')
            ->select('id', 'payment_proof')
            ->get();
        
        // Change column type to TEXT first
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('payment_proof')->nullable()->change();
        });
        
        // Migrate data back: take first payment proof from array
        foreach ($bookings as $booking) {
            $proofs = json_decode($booking->payment_proof, true);
            if (is_array($proofs) && count($proofs) > 0) {
                \Illuminate\Support\Facades\DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'payment_proof' => $proofs[0]['path'] ?? $proofs[0] ?? null
                    ]);
            }
        }
        
        // Change column type back to string
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->change();
        });
    }
};

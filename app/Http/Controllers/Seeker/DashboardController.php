<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display seeker dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Redirect to tenant dashboard if user has occupied booking (verified by admin)
        if ($user->hasActiveBooking() || $user->role === 'tenant') {
            return redirect()->route('tenant.dashboard')
                           ->with('success', 'Selamat! Booking Anda telah dikonfirmasi. Anda sekarang adalah penghuni.');
        }

        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'pending_bookings' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('user_id', $user->id)->where('status', 'confirmed')->count(),
            'rejected_bookings' => Booking::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        $recent_bookings = Booking::where('user_id', $user->id)
                                 ->with('room')
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();

        // Get bookings that need DP payment (pending status without payment proof)
        $bookings_need_dp = Booking::where('user_id', $user->id)
                                   ->where('status', 'pending')
                                   ->where(function($query) {
                                       $query->whereNull('payment_proof')
                                             ->orWhere('payment_proof', '');
                                   })
                                   ->with('room')
                                   ->get();

        $can_become_tenant = $user->canBecomeTenant();

        $available_rooms = Room::where('status', 'available')
                               ->orderBy('room_number')
                               ->get();

        return view('seeker.dashboard', compact('stats', 'recent_bookings', 'can_become_tenant', 'available_rooms', 'bookings_need_dp'));
    }

    /**
     * Show DP payment form for booking
     */
    public function showDpPaymentForm(Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow DP payment for pending bookings
        if ($booking->status !== 'pending') {
            return redirect()->route('seeker.dashboard')
                           ->with('error', 'DP hanya dapat dibayar untuk booking yang statusnya menunggu.');
        }

        $booking->load('room');

        // Calculate remaining DP amount
        $roomPrice = $booking->room->price ?? 0;
        $paidDp = $booking->dp_amount ?? 0;
        $remainingDp = max(0, $roomPrice - $paidDp);
        
        // Auto-fill amount with remaining DP
        // If there's remaining DP, use it. Otherwise use minimum 200000
        if ($remainingDp > 0) {
            $suggestedAmount = $remainingDp; // Use remaining amount to complete the payment
        } else {
            $suggestedAmount = 200000; // Default minimum if no remaining
        }

        return view('seeker.dp-payment-form', compact('booking', 'roomPrice', 'paidDp', 'remainingDp', 'suggestedAmount'));
    }

    /**
     * Process DP payment
     */
    public function processDpPayment(Request $request, Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow DP payment for pending bookings
        if ($booking->status !== 'pending') {
            return redirect()->route('seeker.dashboard')
                           ->with('error', 'DP hanya dapat dibayar untuk booking yang statusnya menunggu.');
        }

        // Calculate remaining DP for validation
        $roomPrice = $booking->room->price ?? 0;
        $currentDp = $booking->dp_amount ?? 0;
        $remainingDp = max(0, $roomPrice - $currentDp);
        
        // Minimum amount is 200000 or remaining amount if less
        $minAmount = $remainingDp > 0 && $remainingDp < 200000 ? $remainingDp : 200000;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minAmount],
            'payment_method' => 'required|in:bank_transfer,dana,gopay',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.min' => 'Jumlah DP minimal Rp ' . number_format($minAmount, 0, ',', '.') . ($remainingDp > 0 && $remainingDp < 200000 ? ' (sisa yang harus dibayar)' : ''),
        ]);

        // Calculate new total DP (add to existing DP)
        $roomPrice = $booking->room->price ?? 0;
        $currentDp = $booking->dp_amount ?? 0;
        $newPayment = $request->amount;
        $newTotalDp = $currentDp + $newPayment;

        // Validate that total DP doesn't exceed room price
        if ($newTotalDp > $roomPrice) {
            return redirect()->back()
                           ->withErrors(['amount' => 'Total DP tidak boleh melebihi harga kosan per bulan (Rp ' . number_format($roomPrice, 0, ',', '.') . ')'])
                           ->withInput();
        }

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('booking-payments', 'public');
            
            // Get existing payment proofs (array)
            $existingProofs = $booking->payment_proof ?? [];
            if (!is_array($existingProofs)) {
                // If old format (string), convert to array
                $existingProofs = $booking->payment_proof ? [[
                    'path' => $booking->payment_proof,
                    'amount' => $currentDp,
                    'created_at' => $booking->created_at->toDateTimeString(),
                ]] : [];
            }
            
            // Add new payment proof to array
            $existingProofs[] = [
                'path' => $path,
                'amount' => $newPayment,
                'created_at' => now()->toDateTimeString(),
            ];
            
            $booking->update([
                'payment_proof' => $existingProofs,
                'dp_amount' => $newTotalDp, // Update dengan total DP (kumulatif)
            ]);
        }

        $message = 'Bukti DP berhasil diupload. ';
        if ($newTotalDp >= $roomPrice) {
            $message .= 'DP sudah lunas! Menunggu verifikasi admin.';
        } else {
            $remaining = $roomPrice - $newTotalDp;
            $message .= 'Sisa yang harus dibayar: Rp ' . number_format($remaining, 0, ',', '.') . '. Menunggu verifikasi admin.';
        }

        return redirect()->route('seeker.dashboard')
                        ->with('success', $message);
    }
}



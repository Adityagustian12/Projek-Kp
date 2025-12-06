<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        // Redirect authenticated users with occupied booking to tenant dashboard
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasActiveBooking() || $user->role === 'tenant') {
                return redirect()->route('tenant.dashboard');
            }
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        $rooms = Room::orderByRaw("FIELD(status, 'available', 'occupied', 'maintenance')")
                    ->orderBy('room_number')
                    ->get();
        
        return view('public.home', compact('rooms'));
    }


    /**
     * Display room details
     */
    public function roomDetail(Room $room)
    {
        $room->load('bookings');
        
        return view('public.room-detail', compact('room'));
    }

    /**
     * Show booking form (requires authentication)
     */
    public function showBookingForm(Room $room)
    {
        if (!auth()->check()) {
            return redirect()->route('register');
        }

        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Admin tidak dapat mengakses form booking.');
        }
        if ($user->role === 'tenant') {
            return redirect()->route('tenant.dashboard')->with('error', 'Penghuni tidak dapat melakukan booking baru.');
        }

        return view('public.booking-form', compact('room'));
    }
}

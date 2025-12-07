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

        // Get rooms directly from database using raw query to bypass any caching
        $roomIds = \DB::table('rooms')
            ->orderByRaw("FIELD(status, 'available', 'occupied', 'maintenance')")
            ->orderBy('room_number')
            ->pluck('id');
        
        // Load rooms fresh from database with all attributes including images
        $rooms = Room::whereIn('id', $roomIds)
                    ->orderByRaw("FIELD(status, 'available', 'occupied', 'maintenance')")
                    ->orderBy('room_number')
                    ->get();
        
        // Sync status for each room to ensure accuracy
        // Note: syncStatus() uses DB::table() so it won't affect model attributes
        foreach ($rooms as $room) {
            // Store images before any operations
            $images = $room->images;
            $room->syncStatus();
            // Ensure images are preserved (syncStatus doesn't touch images column)
            if ($images) {
                $room->setAttribute('images', $images);
            }
        }
        
        return view('public.home', compact('rooms'));
    }


    /**
     * Display room details
     */
    public function roomDetail(Room $room)
    {
        $room->load('bookings');
        
        // Ensure images are properly cast as array
        if ($room->images && !is_array($room->images)) {
            $room->images = json_decode($room->images, true) ?? [];
        }
        
        // Ensure image paths are correct (should be 'rooms/xxx.jpg', not 'storage/rooms/xxx.jpg')
        if ($room->images && is_array($room->images)) {
            $room->images = array_map(function($image) {
                // Remove 'storage/' prefix if present
                $image = ltrim($image, '/');
                if (str_starts_with($image, 'storage/')) {
                    $image = substr($image, 8); // Remove 'storage/' prefix
                }
                // Ensure 'rooms/' prefix if not present
                if (!str_starts_with($image, 'rooms/')) {
                    $image = 'rooms/' . ltrim($image, '/');
                }
                return $image;
            }, $room->images);
        }
        
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

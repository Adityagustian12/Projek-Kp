<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $verifiedPayments = Payment::where('status', 'verified')->sum('amount');
        // Hanya hitung DP yang sudah dikonfirmasi (booking tidak pending)
        $dpSum = Booking::whereNotNull('dp_amount')
                        ->whereIn('status', ['confirmed','occupied','completed'])
                        ->sum('dp_amount');
        $stats = [
            'total_rooms' => Room::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_tenants' => User::where('role', 'tenant')->count(),
            'total_revenue' => 'Rp ' . number_format($verifiedPayments + $dpSum, 0, ',', '.'),
        ];

        $recent_bookings = Booking::with(['user', 'room'])
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();

        $recent_complaints = Complaint::with('user')
                                     ->orderBy('created_at', 'desc')
                                     ->limit(5)
                                     ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_complaints'));
    }

    /**
     * Display rooms management
     */
    public function rooms()
    {
        $rooms = Room::withCount('bookings')->paginate(15);
        
        return view('admin.rooms', compact('rooms'));
    }

    /**
     * Store new room
     */
    public function storeRoom(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'area' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $room = Room::create([
            'room_number' => $request->room_number,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'area' => $request->area,
            'description' => $request->description,
            'status' => 'available',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('rooms', 'public');
                $images[] = $path;
            }
            $room->update(['images' => array_slice($images, 0, 5)]);
        }

        return redirect()->route('admin.rooms')
                        ->with('success', 'Kamar berhasil ditambahkan.');
    }

    /**
     * Update room
     */
    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'area' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $room->update([
            'room_number' => $request->room_number,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'area' => $request->area,
            'description' => $request->description,
        ]);

        // Handle image uploads (append up to 5 total)
        if ($request->hasFile('images')) {
            $existingImages = is_array($room->images) ? $room->images : [];
            $newImages = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('rooms', 'public');
                $newImages[] = $path;
            }
            $merged = array_slice(array_values(array_unique(array_merge($existingImages, $newImages))), 0, 5);
            $room->update(['images' => $merged]);
        }

        return redirect()->route('admin.rooms')
                        ->with('success', 'Kamar berhasil diperbarui.');
    }

    /**
     * Delete room
     */
    public function deleteRoom(Room $room)
    {
        // Check if room has active bookings
        if ($room->bookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return redirect()->route('admin.rooms')
                            ->with('error', 'Tidak dapat menghapus kamar yang memiliki booking aktif.');
        }

        // Check if room has unpaid bills
        if ($room->bills()->where('status', 'pending')->exists()) {
            return redirect()->route('admin.rooms')
                            ->with('error', 'Tidak dapat menghapus kamar yang memiliki tagihan belum dibayar.');
        }

        $room->delete();

        return redirect()->route('admin.rooms')
                        ->with('success', 'Kamar berhasil dihapus.');
    }

    /**
     * Duplicate room
     */
    public function duplicateRoom(Room $room)
    {
        $newRoom = $room->replicate();
        $newRoom->room_number = $room->room_number . '-Copy';
        $newRoom->status = 'available';
        $newRoom->save();

        return redirect()->route('admin.rooms')
                        ->with('success', 'Kamar berhasil diduplikasi.');
    }

    /**
     * Display room details
     */
    public function roomDetail(Room $room)
    {
        $room->load(['bookings.user', 'bills.user']);
        
        return view('admin.room-detail', compact('room'));
    }

    /**
     * Update room status
     */
    public function updateRoomStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $room->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status kamar berhasil diperbarui.');
    }

    /**
     * Display bookings management
     */
    public function bookings()
    {
        $bookings = Booking::with(['user', 'room'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('admin.bookings', compact('bookings'));
    }

    /**
     * Display booking details
     */
    public function bookingDetail(Booking $booking)
    {
        $booking->load(['user', 'room']);
        
        return view('admin.booking-detail', compact('booking'));
    }

    /**
     * Confirm booking
     */
    public function confirmBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Confirm AND move-in in one step
        $booking->update([
            'status' => 'occupied',
            'admin_notes' => $request->admin_notes,
        ]);

        // Update room to occupied (store original capacity once)
        $room = $booking->room;
        if (!$room->original_capacity) {
            $room->update(['original_capacity' => $room->capacity]);
        }
        $room->update([
            'status' => 'occupied',
            'capacity' => 1,
        ]);
        
        // Sync status to ensure consistency
        $room->syncStatus();
        
        // Refresh room model to ensure changes are saved
        $room->refresh();

        // Promote user to tenant if still seeker
        if ($booking->user->role === 'seeker') {
            $booking->user->becomeTenant();
        }

        return redirect()->back()->with('success', 'Booking dikonfirmasi dan penghuni langsung dipindahkan ke kamar.');
    }

    /**
     * Move user into room (change booking to occupied)
     */
    public function moveIntoRoom(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Only confirmed bookings can be moved to occupied
        if ($booking->status !== 'confirmed') {
            return redirect()->back()
                           ->withErrors(['booking' => 'Hanya booking yang sudah dikonfirmasi yang dapat dipindahkan ke kamar.']);
        }

        $booking->update([
            'status' => 'occupied',
            'admin_notes' => $request->admin_notes,
        ]);

        // Update room status to occupied
        $room = $booking->room;
        if (!$room->original_capacity) {
            $room->update(['original_capacity' => $room->capacity]);
        }
        
        $room->update([
            'status' => 'occupied',
            'capacity' => 1
        ]);

        // Change user role from seeker to tenant
        if ($booking->user->role === 'seeker') {
            $booking->user->becomeTenant();
        }

        return redirect()->back()->with('success', 'User berhasil dipindahkan ke kamar dan menjadi penghuni.');
    }


    /**
     * Reject booking
     */
    public function rejectBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Booking berhasil ditolak.');
    }


    /**
     * Display bills management
     */
    public function bills()
    {
        $bills = Bill::with(['user', 'room'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        return view('admin.bills', compact('bills'));
    }

    /**
     * Show create bill form
     */
    public function showCreateBillForm()
    {
        // Get users with role 'tenant' who have occupied bookings (currently living in rooms)
        $tenants = User::where('role', 'tenant')
                      ->whereHas('bookings', function($query) {
                          $query->where('status', 'occupied');
                      })
                      ->with(['bookings' => function($query) {
                          $query->where('status', 'occupied')
                                ->with('room');
                      }])
                      ->get();

        return view('admin.create-bill', compact('tenants'));
    }

    /**
     * Create new bill
     */
    public function createBill(Request $request)
    {
        try {
            \Log::info('Creating bill with data:', $request->all());
            
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date|after_or_equal:' . date('Y-m-d'),
            ]);

            // Check if room exists and is available
            $room = Room::find($request->room_id);
            if (!$room) {
                return redirect()->back()
                                ->withInput()
                                ->with('error', 'Kamar yang dipilih tidak ditemukan.');
            }
            
            // Check if user exists and is a tenant
            $user = User::find($request->user_id);
            if (!$user) {
                return redirect()->back()
                                ->withInput()
                                ->with('error', 'Penghuni yang dipilih tidak ditemukan.');
            }
            
            // Ensure user is a tenant
            if ($user->role !== 'tenant') {
                return redirect()->back()
                                ->withInput()
                                ->with('error', 'Hanya penghuni (tenant) yang dapat ditagih.');
            }

            $dueDate = \Carbon\Carbon::parse($request->due_date);
            $bill = Bill::create([
                'user_id' => $request->user_id,
                'room_id' => $request->room_id,
                'month' => $dueDate->month,
                'year' => $dueDate->year,
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'due_date' => $request->due_date,
                'status' => 'pending',
            ]);

            return redirect()->route('admin.bills')
                            ->with('success', 'Tagihan berhasil dibuat.');
        } catch (\Exception $e) {
            \Log::error('Error creating bill: ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Terjadi kesalahan saat membuat tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Display bill details
     */
    public function billDetail(Bill $bill)
    {
        $bill->load(['user', 'room', 'payments']);
        
        return view('admin.bill-detail', compact('bill'));
    }

    /**
     * Show edit bill form
     */
    public function showEditBillForm(Bill $bill)
    {
        // Prevent editing if already paid with verified payment
        if ($bill->status === 'paid') {
            return redirect()->route('admin.bills.detail', $bill)
                            ->with('error', 'Tagihan yang sudah dibayar tidak dapat diedit.');
        }
        $bill->load(['user', 'room']);
        return view('admin.edit-bill', compact('bill'));
    }

    /**
     * Update bill
     */
    public function updateBill(Request $request, Bill $bill)
    {
        // If bill is already paid, block updates (to keep accounting integrity)
        if ($bill->status === 'paid') {
            return redirect()->route('admin.bills.detail', $bill)
                            ->with('error', 'Tagihan yang sudah dibayar tidak dapat diperbarui.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,overdue,paid',
        ]);

        $dueDate = \Carbon\Carbon::parse($request->due_date);
        $updateData = [
            'amount' => $request->amount,
            'total_amount' => $request->amount,
            'due_date' => $request->due_date,
            'month' => $dueDate->month,
            'year' => $dueDate->year,
            'status' => $request->status,
        ];

        if ($request->status === 'paid') {
            $updateData['paid_at'] = now();
        } else {
            $updateData['paid_at'] = null;
        }

        $bill->update($updateData);

        return redirect()->route('admin.bills')
                        ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Delete bill
     */
    public function deleteBill(Bill $bill)
    {
        // Check if bill has verified payments
        if ($bill->payments()->where('status', 'verified')->exists()) {
            return redirect()->route('admin.bills')
                            ->with('error', 'Tidak dapat menghapus tagihan yang sudah memiliki pembayaran terverifikasi.');
        }

        // Check if bill is already paid
        if ($bill->status === 'paid') {
            return redirect()->route('admin.bills')
                            ->with('error', 'Tidak dapat menghapus tagihan yang sudah dibayar.');
        }

        // Delete all related payments first
        $bill->payments()->delete();

        // Delete the bill
        $bill->delete();

        return redirect()->route('admin.bills')
                        ->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * Display payments management
     */
    public function payments()
    {
        $payments = Payment::with(['user', 'bill', 'verifier'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('admin.payments', compact('payments'));
    }

    /**
     * Display payment details
     */
    public function paymentDetail(Payment $payment)
    {
        $payment->load(['user', 'bill.room', 'verifier']);
        
        return view('admin.payment-detail', compact('payment'));
    }

    /**
     * Verify payment
     */
    public function verifyPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payment->update([
            'status' => $request->status,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        if ($request->status === 'verified') {
            // Update bill status to paid
            $payment->bill->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Get rooms data for API
     */
    public function getRoomsData()
    {
        $rooms = Room::select('id', 'room_number', 'price')->get();
        return response()->json($rooms);
    }

    /**
     * Display complaints management
     */
    public function complaints()
    {
        $complaints = Complaint::with(['user', 'room'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);

        return view('admin.complaints', compact('complaints'));
    }

    /**
     * Display complaint details
     */
    public function complaintDetail(Complaint $complaint)
    {
        $complaint->load(['user', 'room']);
        
        return view('admin.complaint-detail', compact('complaint'));
    }

    /**
     * Update complaint status
     */
    public function updateComplaintStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved',
            'admin_response' => 'nullable|string|max:1000',
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->filled('admin_response')) {
            $data['admin_response'] = $request->admin_response;
        }

        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);

        return redirect()->back()->with('success', 'Status keluhan berhasil diperbarui.');
    }

    /**
     * Delete complaint
     */
    public function deleteComplaint(Complaint $complaint)
    {
        // Hapus keluhan. Jika ada kebutuhan audit, bisa diubah ke soft delete.
        $complaint->delete();
        return redirect()->route('admin.complaints')->with('success', 'Keluhan berhasil dihapus.');
    }

    /**
     * Display tenants management
     */
    public function tenants()
    {
        // Hanya tampilkan tenant yang belum dihapus (soft delete)
        $tenants = User::where('role', 'tenant')
                      ->whereNull('deleted_at')
                      ->withCount(['bookings', 'bills', 'complaints'])
                      ->paginate(15);

        return view('admin.tenants', compact('tenants'));
    }

    /**
     * Display tenant details
     */
    public function tenantDetail(User $tenant)
    {
        $tenant->load(['bookings.room', 'bills.room', 'complaints.room']);
        
        return view('admin.tenant-detail', compact('tenant'));
    }

    /**
     * Delete tenant (soft delete)
     * Otomatis menyelesaikan booking yang masih occupied sebelum nonaktifkan
     */
    public function deleteTenant(User $tenant)
    {
        // Otomatis selesaikan booking yang masih occupied
        $occupiedBookings = $tenant->bookings()
            ->where('status', 'occupied')
            ->get();
        
        foreach ($occupiedBookings as $booking) {
            // Update booking status to completed
            $booking->update([
                'status' => 'completed',
                'check_out_date' => now(),
                'admin_notes' => ($booking->admin_notes ? $booking->admin_notes . "\n\n" : '') . '[Otomatis diselesaikan saat nonaktifkan penghuni]',
            ]);

            // Update room status to available and set capacity to 0
            $room = $booking->room;
            $room->update([
                'status' => 'available',
                'capacity' => 0,
            ]);
        }

        // Check if tenant has confirmed bookings (belum masuk kamar)
        $confirmedBookings = $tenant->bookings()
            ->where('status', 'confirmed')
            ->count();
        
        if ($confirmedBookings > 0) {
            // Cancel confirmed bookings yang belum masuk kamar
            $confirmedBookingList = $tenant->bookings()
                ->where('status', 'confirmed')
                ->get();
            
            foreach ($confirmedBookingList as $booking) {
                $booking->update([
                    'status' => 'cancelled',
                    'admin_notes' => ($booking->admin_notes ? $booking->admin_notes . "\n\n" : '') . '[Dibatalkan saat nonaktifkan penghuni]',
                ]);
            }
        }

        // Check if tenant has unpaid bills
        $unpaidBills = $tenant->bills()->where('status', 'pending')->count();
        if ($unpaidBills > 0) {
            return redirect()->route('admin.tenants')
                            ->with('error', 'Tidak dapat menonaktifkan penghuni yang masih memiliki tagihan belum dibayar. Silakan selesaikan tagihan terlebih dahulu.');
        }

        // Soft delete bookings yang belum completed/rejected/cancelled (pending)
        // Pertahankan booking yang sudah completed untuk riwayat
        $tenant->bookings()
            ->where('status', 'pending')
            ->delete();

        // Hanya hapus bills yang belum dibayar (pending)
        // PERTAHANKAN bills yang sudah paid untuk keperluan akuntansi/riwayat
        $tenant->bills()
            ->where('status', 'pending')
            ->delete();

        // Soft delete complaints yang belum resolved
        // Pertahankan complaints yang sudah resolved untuk riwayat
        $tenant->complaints()
            ->where('status', '!=', 'resolved')
            ->delete();
        
        // Revert user's role to seeker when tenant is deactivated
        if ($tenant->role === 'tenant') {
            $tenant->update(['role' => 'seeker']);
        }
        
        // Soft delete the tenant (data tidak hilang permanen, bisa dikembalikan)
        $tenant->delete();

        $message = 'Penghuni berhasil dinonaktifkan.';
        if ($occupiedBookings->count() > 0) {
            $message .= ' ' . $occupiedBookings->count() . ' booking yang sedang aktif telah otomatis diselesaikan dan kamar tersedia kembali.';
        }
        $message .= ' Data riwayat pembayaran dan transaksi penting tetap tersimpan untuk keperluan akuntansi.';

        // Redirect ke halaman booking jika dipanggil dari booking detail
        if (request()->has('redirect_to') && request()->redirect_to === 'booking' && request()->has('booking_id')) {
            return redirect()->route('admin.bookings.detail', request()->booking_id)
                            ->with('success', $message);
        }

        return redirect()->route('admin.tenants')
                        ->with('success', $message);
    }

}

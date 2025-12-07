<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Storage file access route - MUST be before other routes to avoid conflicts
// This route bypasses all middleware to ensure file access works
Route::get('/storage/{path}', function ($path) {
    // Decode URL-encoded path
    $path = urldecode($path);
    
    // Prevent directory traversal
    $path = str_replace('..', '', $path);
    $path = ltrim($path, '/');
    
    $filePath = storage_path('app/public/' . $path);
    
    // Check if file exists
    if (!file_exists($filePath) || !is_file($filePath)) {
        return response('File not found: ' . $path, 404);
    }
    
    // Get file info
    $fileSize = filesize($filePath);
    $mimeType = mime_content_type($filePath);
    if (!$mimeType) {
        $mimeType = 'application/octet-stream';
    }
    
    // Read file content
    $content = file_get_contents($filePath);
    if ($content === false) {
        return response('Cannot read file', 500);
    }
    
    // Return file content directly with proper headers
    return response($content, 200, [
        'Content-Type' => $mimeType,
        'Content-Length' => $fileSize,
        'Cache-Control' => 'public, max-age=31536000',
        'Accept-Ranges' => 'bytes',
        'X-Content-Type-Options' => 'nosniff',
    ]);
})->where('path', '.*')->name('storage.file')->middleware([]);

// Public Routes (No Authentication Required)
Route::get('/', [PublicController::class, 'index'])->name('public.home');
Route::get('/rooms/{room}', [PublicController::class, 'roomDetail'])->name('public.room.detail');

// Dev utility: hard delete a user by email (LOCAL only)
if (app()->environment('local')) {
    Route::get('/dev/reset-user/{email}', function ($email) {
        User::where('email', $email)->forceDelete();
        return response()->json(['status' => 'ok', 'email' => $email]);
    });
}

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('refresh.csrf');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Authentication Required)
Route::middleware('auth')->group(function () {
    
    // Booking Routes (general authenticated)
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/payment-proof', [BookingController::class, 'uploadPaymentProof'])->name('bookings.payment-proof');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Seeker Routes
    Route::middleware('role:seeker')->prefix('seeker')->name('seeker.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Seeker\DashboardController::class, 'index'])->name('dashboard');
        // Booking creation only for seekers
        Route::get('/rooms/{room}/booking', [PublicController::class, 'showBookingForm'])->name('booking.form');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        // DP Payment routes
        Route::get('/bookings/{booking}/dp-payment', [App\Http\Controllers\Seeker\DashboardController::class, 'showDpPaymentForm'])->name('bookings.dp-payment');
        Route::post('/bookings/{booking}/dp-payment', [App\Http\Controllers\Seeker\DashboardController::class, 'processDpPayment'])->name('bookings.dp-payment.store');
    });

    // Tenant Routes
    Route::middleware('role:tenant')->prefix('tenant')->name('tenant.')->group(function () {
        Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');
        
        // Bills Management
        Route::get('/bills', [TenantDashboardController::class, 'bills'])->name('bills');
        Route::get('/bills/{bill}', [TenantDashboardController::class, 'billDetail'])->name('bills.detail');
        Route::get('/bills/{bill}/payment', [TenantDashboardController::class, 'showPaymentForm'])->name('bills.payment');
        Route::post('/bills/{bill}/payment', [TenantDashboardController::class, 'processPayment'])->name('bills.payment.store');
        
        // Complaints Management
        Route::get('/complaints', [TenantDashboardController::class, 'complaints'])->name('complaints');
        Route::get('/complaints/create', [TenantDashboardController::class, 'showComplaintForm'])->name('complaints.create');
        Route::post('/complaints', [TenantDashboardController::class, 'submitComplaint'])->name('complaints.store');
        Route::get('/complaints/{complaint}', [TenantDashboardController::class, 'complaintDetail'])->name('complaints.detail');
        
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Rooms Management
        Route::get('/rooms', [AdminDashboardController::class, 'rooms'])->name('rooms');
        Route::post('/rooms', [AdminDashboardController::class, 'storeRoom'])->name('rooms.store');
        Route::get('/rooms/{room}', [AdminDashboardController::class, 'roomDetail'])->name('room.detail');
        Route::put('/rooms/{room}', [AdminDashboardController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/{room}', [AdminDashboardController::class, 'deleteRoom'])->name('rooms.delete');
        Route::post('/rooms/{room}/duplicate', [AdminDashboardController::class, 'duplicateRoom'])->name('rooms.duplicate');
        Route::put('/rooms/{room}/status', [AdminDashboardController::class, 'updateRoomStatus'])->name('rooms.status');
        
        // Bookings Management
        Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{booking}', [AdminDashboardController::class, 'bookingDetail'])->name('bookings.detail');
        Route::post('/bookings/{booking}/confirm', [AdminDashboardController::class, 'confirmBooking'])->name('bookings.confirm');
        Route::post('/bookings/{booking}/move-in', [AdminDashboardController::class, 'moveIntoRoom'])->name('bookings.move-in');
        Route::post('/bookings/{booking}/reject', [AdminDashboardController::class, 'rejectBooking'])->name('bookings.reject');
        
        // Bills Management
        Route::get('/bills', [AdminDashboardController::class, 'bills'])->name('bills');
        Route::get('/bills/create', [AdminDashboardController::class, 'showCreateBillForm'])->name('bills.create');
        Route::post('/bills', [AdminDashboardController::class, 'createBill'])->name('bills.store');
        Route::get('/bills/{bill}', [AdminDashboardController::class, 'billDetail'])->name('bills.detail');
        Route::get('/bills/{bill}/edit', [AdminDashboardController::class, 'showEditBillForm'])->name('bills.edit');
        Route::put('/bills/{bill}', [AdminDashboardController::class, 'updateBill'])->name('bills.update');
        Route::delete('/bills/{bill}', [AdminDashboardController::class, 'deleteBill'])->name('bills.delete');
        
        // Payments Management
        Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('payments');
        Route::get('/payments/{payment}', [AdminDashboardController::class, 'paymentDetail'])->name('payments.detail');
        Route::post('/payments/{payment}/verify', [AdminDashboardController::class, 'verifyPayment'])->name('payments.verify');
        
        // Complaints Management
        Route::get('/complaints', [AdminDashboardController::class, 'complaints'])->name('complaints');
        Route::get('/complaints/{complaint}', [AdminDashboardController::class, 'complaintDetail'])->name('complaints.detail');
        Route::put('/complaints/{complaint}/status', [AdminDashboardController::class, 'updateComplaintStatus'])->name('complaints.status');
        Route::delete('/complaints/{complaint}', [AdminDashboardController::class, 'deleteComplaint'])->name('complaints.delete');
        
        // Tenants Management
        Route::get('/tenants', [AdminDashboardController::class, 'tenants'])->name('tenants');
        Route::get('/tenants/{tenant}', [AdminDashboardController::class, 'tenantDetail'])->name('tenants.detail');
        Route::delete('/tenants/{tenant}/delete', [AdminDashboardController::class, 'deleteTenant'])->name('tenants.delete');
        
        // API Routes
        Route::get('/rooms-data', [AdminDashboardController::class, 'getRoomsData'])->name('rooms.data');
    });
});

@extends('layouts.app')

@section('title', 'Dashboard Admin - Kos-Kosan H.Kastim')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="p-3">
                <h6 class="text-muted text-uppercase mb-3">Menu Admin</h6>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('admin.bookings') }}">
                        <i class="fas fa-calendar-check me-2"></i>Kelola Booking
                    </a>
                    <a class="nav-link" href="{{ route('admin.rooms') }}">
                        <i class="fas fa-bed me-2"></i>Kelola Kamar
                    </a>
                    <a class="nav-link" href="{{ route('admin.bills') }}">
                        <i class="fas fa-file-invoice me-2"></i>Kelola Tagihan
                    </a>
                    <a class="nav-link" href="{{ route('admin.payments') }}">
                        <i class="fas fa-credit-card me-2"></i>Kelola Pembayaran
                    </a>
                    <a class="nav-link" href="{{ route('admin.complaints') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Kelola Keluhan
                    </a>
                    <a class="nav-link" href="{{ route('admin.tenants') }}">
                        <i class="fas fa-users me-2"></i>Kelola Penghuni
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                    </h2>
                    <span class="badge bg-primary fs-6">Selamat datang, {{ auth()->user()->name }}</span>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total_rooms'] }}</h4>
                                        <p class="mb-0">Total Kamar</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-bed fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['pending_bookings'] }}</h4>
                                        <p class="mb-0">Booking Pending</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total_tenants'] }}</h4>
                                        <p class="mb-0">Total Penghuni</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total_revenue'] }}</h4>
                                        <p class="mb-0">Total Revenue</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-dollar-sign fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Recent Bookings -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>Booking Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                @forelse($recent_bookings as $booking)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $booking->user ? $booking->user->name : 'User tidak ditemukan' }}</h6>
                                            <p class="mb-1 text-muted">Kamar {{ $booking->room ? $booking->room->room_number : 'N/A' }} - Kapasitas: {{ $booking->room ? $booking->room->capacity : 0 }} orang</p>
                                            <small class="text-muted">Tanggal Masuk: {{ $booking->check_in_date ? $booking->check_in_date->format('d M Y') : 'N/A' }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }}">
                                                {{ [
                                                    'pending' => 'Menunggu',
                                                    'confirmed' => 'Dikonfirmasi',
                                                    'rejected' => 'Ditolak',
                                                    'occupied' => 'Terisi',
                                                ][$booking->status] ?? ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada booking</h5>
                                        <p class="text-muted">Belum ada booking yang dibuat.</p>
                                    </div>
                                @endforelse
                                
                                @if($recent_bookings->count() > 0)
                                    <div class="text-center">
                                        <a href="{{ route('admin.bookings') }}" class="btn btn-outline-primary btn-sm">
                                            Lihat Semua Booking
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Keluhan Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                @forelse($recent_complaints as $complaint)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $complaint->title }}</h6>
                                            <p class="mb-1 text-muted">{{ $complaint->user ? $complaint->user->name : 'User tidak ditemukan' }}</p>
                                            <small class="text-muted">{{ $complaint->created_at ? $complaint->created_at->format('d M Y') : 'N/A' }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $complaint->status === 'pending' ? 'warning' : ($complaint->status === 'resolved' ? 'success' : 'info') }}">
                                                {{ [
                                                    'new' => 'Baru',
                                                    'pending' => 'Menunggu',
                                                    'in_progress' => 'Diproses',
                                                    'resolved' => 'Selesai',
                                                    'closed' => 'Ditutup',
                                                ][$complaint->status] ?? ucfirst($complaint->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5 class="text-muted">Tidak ada keluhan</h5>
                                        <p class="text-muted">Semua keluhan sudah ditangani.</p>
                                    </div>
                                @endforelse
                                
                                @if($recent_complaints->count() > 0)
                                    <div class="text-center">
                                        <a href="{{ route('admin.complaints') }}" class="btn btn-outline-warning btn-sm">
                                            Lihat Semua Keluhan
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar {
    background-color: #f8f9fa;
    min-height: 100vh;
    border-right: 1px solid #dee2e6;
}

.main-content {
    background-color: #fff;
}

.nav-link {
    color: #495057;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin-bottom: 0.25rem;
}

.nav-link:hover {
    background-color: #e9ecef;
    color: #495057;
}

.nav-link.active {
    background-color: #0d6efd;
    color: white;
}
</style>
@endsection
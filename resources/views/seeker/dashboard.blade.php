@extends('layouts.app')

@section('title', 'Dashboard Pencari Kosan - Kos-Kosan H.Kastim')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="p-3">
                <h6 class="text-muted text-uppercase mb-3">Menu Pencari Kosan</h6>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('seeker.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('bookings.my') }}">
                        <i class="fas fa-calendar-check me-2"></i>Booking Saya
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-search me-2"></i>Dashboard Pencari Kosan
                    </h2>
                    <span class="badge bg-primary fs-6">Selamat datang, {{ auth()->user()->name }}</span>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Important Notice: DP Payment Required -->
                @if($bookings_need_dp->count() > 0)
                    <div class="alert alert-warning border-warning border-2 alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Penting: Pelunasan DP Diperlukan
                                </h5>
                                <p class="mb-2">
                                    <strong>Calon penghuni harus melunasi DP (Down Payment) terlebih dahulu</strong> untuk memproses booking Anda. 
                                    Booking tidak akan diproses oleh admin sebelum DP dilunasi dan diverifikasi.
                                </p>
                                <p class="mb-2">
                                    <strong>Anda memiliki {{ $bookings_need_dp->count() }} booking yang belum melunasi DP:</strong>
                                </p>
                                <ul class="mb-2">
                                    @foreach($bookings_need_dp as $booking)
                                        <li>
                                            <strong>Kamar {{ $booking->room->room_number }}</strong> - 
                                            <a href="{{ route('seeker.bookings.dp-payment', $booking) }}" class="alert-link fw-bold">
                                                Klik di sini untuk melunasi DP
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <hr class="my-2">
                                <p class="mb-0 small">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Tips:</strong> Setelah melakukan transfer, segera upload bukti pembayaran untuk mempercepat proses verifikasi.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif($stats['pending_bookings'] > 0)
                    <div class="alert alert-info border-info border-2 alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Penting
                                </h5>
                                <p class="mb-0">
                                    <strong>Calon penghuni harus melunasi DP (Down Payment) terlebih dahulu</strong> untuk memproses booking. 
                                    Pastikan Anda telah melunasi DP dan menunggu verifikasi dari admin.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Statistics Cards (match admin style) -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total_bookings'] }}</h4>
                                        <p class="mb-0">Total Booking</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-alt fa-2x"></i>
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
                                        <p class="mb-0">Booking Menunggu</p>
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
                                        <h4 class="mb-0">{{ $stats['confirmed_bookings'] }}</h4>
                                        <p class="mb-0">Booking Dikonfirmasi</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['rejected_bookings'] }}</h4>
                                        <p class="mb-0">Booking Ditolak</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-times-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Booking Terbaru</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recent_bookings as $booking)
                            @php
                                $needsDp = $booking->status === 'pending' && (!$booking->payment_proof || $booking->payment_proof === '');
                            @endphp
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom {{ $needsDp ? 'bg-light p-3 rounded border-warning border-start border-3' : '' }}">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        {{ $booking->room->room_number }}
                                        @if($needsDp)
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-exclamation-circle me-1"></i>Belum Lunas DP
                                            </span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $booking->created_at->format('d M Y') }}</small>
                                    @if($booking->dp_amount)
                                        <div class="mt-1">
                                            <small class="text-muted">DP: Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</small>
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <small class="text-danger fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i>DP Belum Dibayar
                                            </small>
                                        </div>
                                    @endif
                                    @if($needsDp)
                                        <div class="mt-2">
                                            <small class="text-danger">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <strong>Segera lunasi DP untuk memproses booking Anda!</strong>
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 d-flex flex-column align-items-end gap-2">
                                    <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }}">
                                        {{ [
                                            'pending' => 'Menunggu',
                                            'confirmed' => 'Dikonfirmasi',
                                            'rejected' => 'Ditolak',
                                            'occupied' => 'Terisi',
                                        ][$booking->status] ?? ucfirst($booking->status) }}
                                    </span>
                                    @if($booking->status === 'pending')
                                        <a href="{{ route('seeker.bookings.dp-payment', $booking) }}" class="btn btn-sm btn-success {{ $needsDp ? 'shadow-sm' : '' }}">
                                            <i class="fas fa-money-bill-wave me-1"></i>Lunasi DP
                                        </a>
                                        @if($booking->payment_proof)
                                            <span class="badge bg-info">
                                                <i class="fas fa-check me-1"></i>DP Terkirim
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">Belum ada booking</div>
                        @endforelse
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

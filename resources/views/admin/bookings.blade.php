@extends('layouts.app')

@section('title', 'Kelola Booking - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="p-3">
                <h6 class="text-muted text-uppercase mb-3">Menu Admin</h6>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link active" href="{{ route('admin.bookings') }}">
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
                        <i class="fas fa-calendar-check me-2"></i>Kelola Booking
                    </h2>
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

                <!-- Bookings Table -->
                <div class="card">
                    <div class="card-body">
                        @if($bookings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Pemesan</th>
                                            <th>Kamar</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Status</th>
                                            <th>DP Masuk</th>
                                            <th>Tanggal Booking</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $booking)
                                            <tr>
                                                <td>{{ $booking->id }}</td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $booking->user ? $booking->user->name : 'User tidak ditemukan' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $booking->user ? $booking->user->email : 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $booking->room ? $booking->room->room_number : 'Kamar tidak ditemukan' }}</strong>
                                                        <br>
                                                        <small class="text-muted">Kapasitas: {{ $booking->room ? $booking->room->capacity : 0 }} orang</small>
                                                    </div>
                                                </td>
                                                <td>{{ $booking->check_in_date ? $booking->check_in_date->format('d M Y') : 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : ($booking->status === 'occupied' ? 'secondary' : ($booking->status === 'completed' ? 'primary' : 'danger'))) }}">
                                                        {{ [
                                                            'pending' => 'Menunggu',
                                                            'confirmed' => 'Dikonfirmasi',
                                                            'rejected' => 'Ditolak',
                                                            'occupied' => 'Terisi',
                                                            'cancelled' => 'Dibatalkan',
                                                            'completed' => 'Selesai',
                                                        ][$booking->status] ?? ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $dpCanShow = in_array($booking->status, ['confirmed','occupied','completed']);
                                                        $dpRejected = in_array($booking->status, ['rejected','cancelled']);
                                                    @endphp
                                                    @if($dpCanShow && !is_null($booking->dp_amount))
                                                        <span class="badge rounded-pill bg-primary text-white fw-semibold">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                                                    @elseif($dpRejected && !is_null($booking->dp_amount))
                                                        <span class="badge rounded-pill bg-danger">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                                                    @elseif($booking->status === 'pending' && !is_null($booking->dp_amount))
                                                        <span class="badge rounded-pill bg-secondary">Menunggu</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $booking->created_at ? $booking->created_at->format('d M Y H:i') : 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('admin.bookings.detail', $booking) }}" 
                                                           class="btn btn-outline-info" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination (simplified) -->
                            <div class="d-flex flex-column align-items-center mt-4">
                                <nav aria-label="Navigasi halaman">
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $bookings->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $bookings->previousPageUrl() ?: '#' }}" tabindex="-1">Sebelumnya</a>
                                        </li>
                                        @for ($i = max(1, $bookings->currentPage() - 1); $i <= min($bookings->lastPage(), $bookings->currentPage() + 1); $i++)
                                            <li class="page-item {{ $i === $bookings->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{ $bookings->currentPage() === $bookings->lastPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $bookings->nextPageUrl() ?: '#' }}">Berikutnya</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak ada booking</h4>
                                <p class="text-muted">Belum ada booking yang dibuat.</p>
                            </div>
                        @endif
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection

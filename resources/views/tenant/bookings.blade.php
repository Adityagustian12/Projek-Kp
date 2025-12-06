@extends('layouts.app')

@section('title', 'Booking Saya - Penghuni Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="p-3">
                <h6 class="text-muted text-uppercase mb-3">Menu Penghuni</h6>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('tenant.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link active" href="{{ route('bookings.my') }}">
                        <i class="fas fa-calendar-check me-2"></i>Booking Saya
                    </a>
                    <a class="nav-link" href="{{ route('tenant.bills') }}">
                        <i class="fas fa-file-invoice me-2"></i>Tagihan
                    </a>
                    <a class="nav-link" href="{{ route('tenant.complaints') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Komplain
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Booking Saya
                    </h2>
                    <a href="{{ route('public.home') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Booking Baru
                    </a>
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
                                            <th>Kamar</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Status</th>
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
                                                        <strong>{{ $booking->room->room_number }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $booking->room->description }}</small>
                                                    </div>
                                                </td>
                                                <td>{{ $booking->move_in_date ? $booking->move_in_date->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @switch($booking->status)
                                                        @case('pending')
                                                            <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                                            @break
                                                        @case('confirmed')
                                                            <span class="badge bg-success">Dikonfirmasi</span>
                                                            @break
                                                        @case('rejected')
                                                            <span class="badge bg-danger">Ditolak</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge bg-info">Selesai</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('bookings.show', $booking) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                        
                                                        @if($booking->status === 'pending')
                                                            <form action="{{ route('bookings.cancel', $booking) }}" 
                                                                  method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-times"></i> Batal
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bookings->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum Ada Booking</h4>
                                <p class="text-muted">Anda belum melakukan booking kamar kos.</p>
                                <a href="{{ route('public.home') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Booking Kamar Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

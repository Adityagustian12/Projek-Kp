@extends('layouts.app')

@section('title', 'Beranda - Kos Kosan H.Kastim')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row bg-primary text-white py-4 py-md-5">
        <div class="col-12 text-center px-3">
            <h1 class="display-4 fw-bold mb-0">Selamat Datang di Kos Kosan H.Kastim</h1>
        </div>
    </div>

    <!-- Available Rooms -->
    <div class="row py-3 py-md-4">
        <div class="col-12 px-3">
            <h2 class="mb-3 mb-md-4">
                <i class="fas fa-bed me-2"></i>Daftar Kamar
            </h2>
        </div>
    </div>

    <div class="row g-3 g-md-4 px-2 px-md-0">
        @forelse($rooms as $room)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 card-hover shadow-sm">
                    @if($room->images && count($room->images) > 0)
                        <img src="{{ asset('storage/' . $room->images[0]) }}" class="card-img-top" alt="Room {{ $room->room_number }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2">{{ $room->room_number }}</h5>
                        @if($room->description)
                            <p class="card-text text-muted small mb-3">{{ Str::limit($room->description, 80) }}</p>
                        @endif
                        
                        <div class="mt-auto">
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-bed me-1"></i>{{ $room->capacity }} Orang
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h4 class="text-primary mb-0" style="font-size: 1.1rem;">Rp {{ number_format($room->price, 0, ',', '.') }}/bulan</h4>
                                @php
                                    $statusClass = $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'warning' : 'danger');
                                    $statusLabel = $room->status === 'available' ? 'Tersedia' : ($room->status === 'occupied' ? 'Terisi' : 'Perawatan');
                                @endphp
                                <span class="badge bg-{{ $statusClass }} status-badge">{{ $statusLabel }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent px-3 py-2">
                        <div class="d-grid gap-2">
                            <a href="{{ route('public.room.detail', $room) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            @if($room->status === 'available')
                                @auth
                                    @if(auth()->user()->isSeeker() || auth()->user()->isTenant())
                                        <a href="{{ route('seeker.booking.form', $room) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('seeker.booking.form', $room) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-sign-in-alt me-2"></i>Booking Sekarang
                                    </a>
                                @endauth
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-ban me-2"></i>Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-bed fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum ada data kamar</h4>
                    <p class="text-muted">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

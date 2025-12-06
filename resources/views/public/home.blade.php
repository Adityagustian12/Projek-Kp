@extends('layouts.app')

@section('title', 'Beranda - Kos Kosan H.Kastim')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row bg-primary text-white py-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold">Selamat Datang di Kos Kosan H.Kastim</h1>
        </div>
    </div>

    <!-- Available Rooms -->
    <div class="row py-4">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-bed me-2"></i>Daftar Kamar
            </h2>
        </div>
    </div>

    <div class="row">
        @forelse($rooms as $room)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 card-hover shadow-sm">
                    @if($room->images && count($room->images) > 0)
                        <img src="{{ asset('storage/' . $room->images[0]) }}" class="card-img-top" alt="Room {{ $room->room_number }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $room->room_number }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($room->description, 100) }}</p>
                        
                        <div class="mt-auto">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-bed me-1"></i>{{ $room->capacity }} Orang
                                    </small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="text-primary mb-0">Rp {{ number_format($room->price, 0, ',', '.') }}/bulan</h4>
                                @php
                                    $statusClass = $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'warning' : 'danger');
                                    $statusLabel = $room->status === 'available' ? 'Tersedia' : ($room->status === 'occupied' ? 'Terisi' : 'Perawatan');
                                @endphp
                                <span class="badge bg-{{ $statusClass }} status-badge">{{ $statusLabel }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('public.room.detail', $room) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            @if($room->status === 'available')
                                @auth
                                    @if(auth()->user()->isSeeker() || auth()->user()->isTenant())
                                        <a href="{{ route('seeker.booking.form', $room) }}" class="btn btn-primary">
                                            <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('seeker.booking.form', $room) }}" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Booking Sekarang
                                    </a>
                                @endauth
                            @else
                                <button class="btn btn-secondary" disabled>
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

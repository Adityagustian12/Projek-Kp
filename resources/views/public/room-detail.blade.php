@extends('layouts.app')

@section('title', 'Detail Kamar - Kos-Kosan H.Kastim')

@section('content')
<div class="container mt-4 mt-md-5">
    

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $room->room_number }}</h2>
                    
                    @if($room->images && count($room->images) > 0)
                        <div class="mb-3">
                            <div class="row g-2">
                                @foreach($room->images as $image)
                                    <div class="col-6 col-md-4">
                                        @php
                                            // Use asset() directly - more reliable
                                            $imageUrl = asset('storage/' . $image);
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                             class="img-fluid rounded shadow-sm" 
                                             alt="Room {{ $room->room_number }}"
                                             style="height: 180px; width: 100%; object-fit: cover;"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-4 bg-light p-5 text-center rounded">
                            <i class="fas fa-image fa-4x text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada gambar tersedia</p>
                        </div>
                    @endif

                    <h5 class="mb-2">Deskripsi</h5>
                    <p class="text-muted mb-0">{{ $room->description ?: 'Tidak ada deskripsi tersedia.' }}</p>

                    
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 84px;">
                <div class="card-body">
                    <h4 class="card-title mb-3">Informasi Kamar</h4>
                    
                    <div class="mb-3">
                        <strong>Nomor Kamar:</strong>
                        <span class="text-muted">{{ $room->room_number }}</span>
                    </div>
                    
                    
                    <div class="mb-3">
                        <strong>Kapasitas:</strong>
                        <span class="text-muted">{{ $room->capacity }} Orang</span>
                    </div>
                    
                    
                    @if($room->area)
                        <div class="mb-3">
                            <strong>Luas:</strong>
                            <span class="text-muted">{{ $room->area }}</span>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <strong>Status:</strong>
                        @php
                            $statusClass = $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'warning' : 'danger');
                            $statusLabel = $room->status === 'available' ? 'Tersedia' : ($room->status === 'occupied' ? 'Terisi' : 'Perawatan');
                        @endphp
                        <span class="badge bg-{{ $statusClass }} ms-2">{{ $statusLabel }}</span>
                    </div>

                    <hr>

                    <div class="text-center mb-4">
                        <h3 class="text-primary">Rp {{ number_format($room->price, 0, ',', '.') }}</h3>
                        <small class="text-muted">per bulan</small>
                    </div>

                    <div class="d-grid gap-2">
                        @if($room->status === 'available')
                            @auth
                                @if(auth()->user()->isSeeker() || auth()->user()->isTenant())
                                    <a href="{{ route('seeker.booking.form', $room) }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('seeker.booking.form', $room) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Booking Sekarang
                                </a>
                            @endauth
                        @else
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-ban me-2"></i>Tidak Tersedia
                            </button>
                        @endif

                        <a href="{{ route('public.home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

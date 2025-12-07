@extends('layouts.app')

@section('title', 'Detail Kamar - Admin Dashboard')

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
                    <a class="nav-link" href="{{ route('admin.bookings') }}">
                        <i class="fas fa-calendar-check me-2"></i>Kelola Booking
                    </a>
                    <a class="nav-link active" href="{{ route('admin.rooms') }}">
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
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-bed me-2"></i>Detail Kamar {{ $room->room_number }}
                        </h2>
                        
                    </div>
                    <div>
                        <a href="{{ route('admin.rooms') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button class="btn btn-warning me-2" onclick="editRoom({{ $room->id }})">
                            <i class="fas fa-edit me-2"></i>Edit Kamar
                        </button>
                        <button class="btn btn-success me-2" onclick="duplicateRoom({{ $room->id }})">
                            <i class="fas fa-copy me-2"></i>Duplikasi
                        </button>
                        <button class="btn btn-danger" onclick="deleteRoom({{ $room->id }})">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </div>
                </div>

                <div class="row">
                    <!-- Room Information -->
                    <div class="col-lg-8">
                        <!-- Basic Info Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Kamar
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nomor Kamar:</strong>
                                        <p class="text-muted mb-0">{{ $room->room_number }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Harga Sewa:</strong>
                                        <p class="text-muted mb-0">Rp {{ number_format($room->price, 0, ',', '.') }}/bulan</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Kapasitas:</strong>
                                        <p class="text-muted mb-0">{{ $room->capacity }} orang</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Luas Kamar:</strong>
                                        <p class="text-muted mb-0">{{ $room->area ?? 'Tidak ditentukan' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'warning' : 'danger') }}">
                                            {{ $room->status === 'available' ? 'Tersedia' : ($room->status === 'occupied' ? 'Terisi' : 'Maintenance') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Tanggal Dibuat:</strong>
                                        <p class="text-muted mb-0">{{ $room->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                @if($room->description)
                                    <div class="mt-3">
                                        <strong>Deskripsi:</strong>
                                        <p class="text-muted">{{ $room->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Facilities Card -->
                        @if($room->facilities && count($room->facilities) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Fasilitas
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($room->facilities as $facility)
                                        <div class="col-md-4 mb-2">
                                            <span class="badge bg-primary me-2">
                                                <i class="fas fa-check me-1"></i>{{ $facility }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Images Card -->
                        @if($room->images && count($room->images) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-images me-2"></i>Gambar Kamar
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($room->images as $image)
                                        <div class="col-md-4 mb-3">
                                            <img src="{{ storage_url($image) }}" 
                                                 class="img-fluid rounded" 
                                                 alt="Gambar Kamar {{ $room->room_number }}"
                                                 style="height: 200px; width: 100%; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">

                        <!-- Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Statistik
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <h4 class="text-primary mb-0">{{ $room->bookings->count() }}</h4>
                                        <small class="text-muted">Total Booking</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="text-success mb-0">{{ $room->bills->count() }}</h4>
                                        <small class="text-muted">Total Tagihan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Bookings -->
                        @if($room->bookings->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>Booking Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($room->bookings->take(3) as $booking)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $booking->user->name }}</h6>
                                            <p class="mb-1 text-muted">{{ $booking->check_in_date->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($room->bookings->count() > 3)
                                    <div class="text-center">
                                        <a href="{{ route('admin.bookings') }}?room={{ $room->id }}" class="btn btn-outline-primary btn-sm">
                                            Lihat Semua Booking
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">
                    <i class="fas fa-sync me-2"></i>Ubah Status Kamar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Baru</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Terisi</option>
                            <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Status
                    </button>
                </div>
            </form>
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

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}
</style>

<script>
// Room data untuk JavaScript
const room = @json($room);

function editRoom(roomId) {
    window.location.href = `/admin/rooms?edit=${roomId}`;
}

function deleteRoom(roomId) {
    if (confirm(`Apakah Anda yakin ingin menghapus kamar ${room.room_number}?\n\nPerhatian: Kamar yang memiliki booking aktif atau tagihan belum dibayar tidak dapat dihapus.`)) {
        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/rooms/${roomId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function duplicateRoom(roomId) {
    if (confirm(`Apakah Anda yakin ingin menduplikasi kamar ${room.room_number}?\n\nKamar baru akan dibuat dengan nomor "${room.room_number}-Copy"`)) {
        // Create form for POST request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/rooms/${roomId}/duplicate`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function updateRoomStatus(roomId) {
    // Set form action
    document.getElementById('updateStatusForm').action = `/admin/rooms/${roomId}/status`;
    
    // Show modal
    const statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    statusModal.show();
}

// Show success/error messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endsection

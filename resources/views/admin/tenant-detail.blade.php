@extends('layouts.app')

@section('title', 'Detail Penghuni - Admin Dashboard')

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
                    <a class="nav-link active" href="{{ route('admin.tenants') }}">
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
                            <i class="fas fa-user me-2"></i>Detail Penghuni: {{ $tenant->name }}
                        </h2>
                        
                    </div>
                    <div>
                        <a href="{{ route('admin.tenants') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Tenant Information -->
                    <div class="col-lg-8">
                        <!-- Basic Info Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center mb-4">
                                        @if($tenant->profile_picture)
                                            <img src="{{ storage_url($tenant->profile_picture) }}" 
                                                 class="rounded-circle mb-3" 
                                                 width="120" height="120" 
                                                 alt="Foto {{ $tenant->name }}">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                                 style="width: 120px; height: 120px; font-size: 48px;">
                                                {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <h4 class="mb-1">{{ $tenant->name }}</h4>
                                        <p class="text-muted mb-0">{{ $tenant->email }}</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <strong>Email:</strong>
                                                <p class="text-muted mb-0">{{ $tenant->email }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <strong>Telepon:</strong>
                                                <p class="text-muted mb-0">{{ $tenant->phone ?? 'Tidak ada' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <strong>Tanggal Lahir:</strong>
                                                <p class="text-muted mb-0">{{ $tenant->birth_date ? $tenant->birth_date->format('d M Y') : 'Tidak ada' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <strong>Jenis Kelamin:</strong>
                                                <p class="text-muted mb-0">
                                                    @php
                                                        $genderMap = ['male' => 'Laki-laki', 'female' => 'Perempuan'];
                                                    @endphp
                                                    {{ $tenant->gender ? ($genderMap[$tenant->gender] ?? ucfirst($tenant->gender)) : 'Tidak ada' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <strong>Pekerjaan:</strong>
                                                <p class="text-muted mb-0">{{ $tenant->occupation ?? 'Tidak ada' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <strong>NIK:</strong>
                                                <p class="text-muted mb-0">{{ $tenant->id_card_number ?? 'Tidak ada' }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($tenant->address)
                                            <div class="mt-3">
                                                <strong>Alamat:</strong>
                                                <p class="text-muted">{{ $tenant->address }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Card -->
                        @if($tenant->emergency_contact_name || $tenant->emergency_contact_phone)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-phone me-2"></i>Kontak Darurat
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nama Kontak Darurat:</strong>
                                        <p class="text-muted mb-0">{{ $tenant->emergency_contact_name ?? 'Tidak ada' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Telepon Kontak Darurat:</strong>
                                        <p class="text-muted mb-0">{{ $tenant->emergency_contact_phone ?? 'Tidak ada' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Current Room Card -->
                        @php
                            $currentBooking = $tenant->bookings()->where('status', 'confirmed')->latest()->first();
                        @endphp
                        @if($currentBooking)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bed me-2"></i>Kamar Saat Ini
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nomor Kamar:</strong>
                                        <p class="text-muted mb-0">
                                            <span class="badge bg-info fs-6">{{ $currentBooking->room->room_number }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Harga Sewa:</strong>
                                        <p class="text-muted mb-0">Rp {{ number_format($currentBooking->room->price, 0, ',', '.') }}/bulan</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Tanggal Masuk:</strong>
                                        <p class="text-muted mb-0">{{ $currentBooking->check_in_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Kapasitas Kamar:</strong>
                                        <p class="text-muted mb-0">{{ $currentBooking->room->capacity }} orang</p>
                                    </div>
                                </div>
                                
                                @if($currentBooking->room->description)
                                    <div class="mt-3">
                                        <strong>Deskripsi Kamar:</strong>
                                        <p class="text-muted">{{ $currentBooking->room->description }}</p>
                                    </div>
                                @endif
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
                                        <h4 class="text-primary mb-0">{{ $tenant->bookings->count() }}</h4>
                                        <small class="text-muted">Total Booking</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="text-success mb-0">{{ $tenant->bills->count() }}</h4>
                                        <small class="text-muted">Total Tagihan</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="text-warning mb-0">{{ $tenant->complaints->count() }}</h4>
                                        <small class="text-muted">Total Keluhan</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="text-info mb-0">{{ $tenant->payments->count() }}</h4>
                                        <small class="text-muted">Total Pembayaran</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Aktivitas Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $recentActivities = collect();
                                    
                                    // Add recent bookings
                                    foreach($tenant->bookings->take(2) as $booking) {
                                        $recentActivities->push([
                                            'type' => 'booking',
                                            'title' => 'Booking Kamar ' . $booking->room->room_number,
                                            'date' => $booking->created_at,
                                            'status' => $booking->status,
                                            'icon' => 'fas fa-calendar-check',
                                            'color' => $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger')
                                        ]);
                                    }
                                    
                                    // Add recent complaints
                                    foreach($tenant->complaints->take(2) as $complaint) {
                                        $recentActivities->push([
                                            'type' => 'complaint',
                                            'title' => 'Keluhan: ' . Str::limit($complaint->title, 20),
                                            'date' => $complaint->created_at,
                                            'status' => $complaint->status,
                                            'icon' => 'fas fa-exclamation-triangle',
                                            'color' => $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'new' ? 'primary' : 'warning')
                                        ]);
                                    }
                                    
                                    $recentActivities = $recentActivities->sortByDesc('date')->take(5);
                                @endphp
                                
                                @forelse($recentActivities as $activity)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="{{ $activity['icon'] }} fa-2x text-{{ $activity['color'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                            <small class="text-muted">{{ $activity['date']->format('d M Y H:i') }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @php
                                                $label = $activity['status'];
                                                if ($activity['type'] === 'booking') {
                                                    $label = [
                                                        'pending' => 'Menunggu',
                                                        'confirmed' => 'Dikonfirmasi',
                                                        'rejected' => 'Ditolak',
                                                        'occupied' => 'Terisi',
                                                        'completed' => 'Selesai',
                                                        'cancelled' => 'Dibatalkan',
                                                    ][$activity['status']] ?? ucfirst($activity['status']);
                                                } else {
                                                    $label = [
                                                        'new' => 'Baru',
                                                        'in_progress' => 'Diproses',
                                                        'resolved' => 'Selesai',
                                                        'closed' => 'Ditutup',
                                                    ][$activity['status']] ?? ucfirst($activity['status']);
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $activity['color'] }}">{{ $label }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3">
                                        <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada aktivitas</p>
                                    </div>
                                @endforelse
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

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}

.table img {
    object-fit: cover;
}
</style>

<script>
// fungsi edit dan riwayat dihapus sesuai permintaan

function createBill(tenantId) {
    // Redirect to create bill with tenant pre-selected
    window.location.href = `/admin/bills/create?tenant=${tenantId}`;
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

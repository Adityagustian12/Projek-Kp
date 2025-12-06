@extends('layouts.app')

@section('title', 'Kelola Penghuni - Admin Dashboard')

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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-users me-2"></i>Kelola Penghuni
                    </h2>
                </div>


                <!-- Tenants Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Penghuni
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($tenants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Foto</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Telepon</th>
                                            <th>Kamar</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenants as $tenant)
                                            <tr>
                                                <td>
                                                    @if($tenant->profile_picture)
                                                        <img src="{{ asset('storage/' . $tenant->profile_picture) }}" 
                                                             class="rounded-circle" 
                                                             width="40" height="40" 
                                                             alt="Foto {{ $tenant->name }}">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $tenant->name }}</strong>
                                                        @if($tenant->birth_date)
                                                            <br>
                                                            <small class="text-muted">{{ $tenant->birth_date->format('d M Y') }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $tenant->email }}</strong>
                                                        @if($tenant->address)
                                                            <br>
                                                            <small class="text-muted">{{ Str::limit($tenant->address, 30) }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $tenant->phone ?? '-' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $currentRoom = $tenant->bookings()
                                                            ->where('status', 'occupied')
                                                            ->with('room')
                                                            ->latest()
                                                            ->first();
                                                    @endphp
                                                    @if($currentRoom)
                                                        <span class="badge bg-info">{{ $currentRoom->room->room_number }}</span>
                                                        <br>
                                                        <small class="text-muted">Rp {{ number_format($currentRoom->room->price, 0, ',', '.') }}/bulan</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($currentRoom)
                                                        {{ $currentRoom->check_in_date->format('d M Y') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-info" onclick="viewTenant({{ $tenant->id }})" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTenant({{ $tenant->id }})" title="Nonaktifkan Penghuni">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $tenants->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada penghuni</h4>
                                <p class="text-muted">Belum ada penghuni yang terdaftar di sistem.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Cards (simplified) -->
                @if($tenants->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $tenants->count() }}</h4>
                                        <p class="mb-0">Total Penghuni</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $tenants->filter(function($tenant) { return $tenant->complaints()->exists(); })->count() }}</h4>
                                        <p class="mb-0">Punya Keluhan</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
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

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.table img {
    object-fit: cover;
}
</style>

<script>
function viewTenant(tenantId) {
    // Redirect to tenant detail page
    window.location.href = `/admin/tenants/${tenantId}`;
}

// fungsi edit dan riwayat dihapus sesuai permintaan

function deleteTenant(tenantId) {
    if (confirm('⚠️ PERINGATAN: Apakah Anda yakin ingin menonaktifkan penghuni ini?\n\n' +
                'Tindakan ini akan:\n' +
                '• Menonaktifkan akun penghuni (data tidak hilang permanen)\n' +
                '• Otomatis menyelesaikan booking yang sedang aktif (kamar akan tersedia kembali)\n' +
                '• Membatalkan booking yang sudah dikonfirmasi tapi belum masuk kamar\n' +
                '• Menghapus booking yang masih pending\n' +
                '• Menghapus tagihan yang belum dibayar\n' +
                '• Menghapus keluhan yang belum selesai\n' +
                '• Mengubah role pengguna kembali menjadi "Pencari Kosan"\n\n' +
                '✅ Data yang DIPERTAHANKAN:\n' +
                '• Riwayat pembayaran yang sudah dibayar\n' +
                '• Booking yang sudah selesai (completed)\n' +
                '• Keluhan yang sudah diselesaikan\n\n' +
                'Data dapat dikembalikan jika diperlukan.\n\n' +
                'Ketik "NONAKTIFKAN" untuk konfirmasi:')) {
        
        const confirmation = prompt('Ketik "NONAKTIFKAN" untuk konfirmasi:');
        if (confirmation === 'NONAKTIFKAN') {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/tenants/${tenantId}/delete`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        } else {
            alert('Tindakan dibatalkan.');
        }
    }
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

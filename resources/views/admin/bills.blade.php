@extends('layouts.app')

@section('title', 'Kelola Tagihan - Admin Dashboard')

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
                    <a class="nav-link active" href="{{ route('admin.bills') }}">
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
                        <i class="fas fa-file-invoice me-2"></i>Kelola Tagihan
                    </h2>
                    <div>
                        <a href="{{ route('admin.bills.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Buat Tagihan Baru
                        </a>
                    </div>
                </div>


                <!-- Bills Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Tagihan
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($bills->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Tagihan</th>
                                            <th>Penghuni</th>
                                            <th>Kamar</th>
                                            <th>Periode</th>
                                            <th>Total</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bills as $bill)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $bill->id }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $bill->created_at ? $bill->created_at->format('d M Y') : 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $bill->user ? $bill->user->name : 'User tidak ditemukan' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $bill->user ? $bill->user->email : 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $bill->room ? $bill->room->room_number : 'Kamar tidak ditemukan' }}</span>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::create()->month($bill->month)->format('F') }} {{ $bill->year }}
                                                </td>
                                                <td>
                                                    <strong>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</strong>
                                                </td>
                                                <td>
                                                    <span class="{{ $bill->due_date && $bill->due_date < now() && $bill->status !== 'paid' ? 'text-danger' : '' }}">
                                                        {{ $bill->due_date ? $bill->due_date->format('d M Y') : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($bill->status === 'paid')
                                                        <span class="badge bg-success">Sudah Dibayar</span>
                                                    @elseif($bill->due_date && $bill->due_date < now())
                                                        <span class="badge bg-danger">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Dibayar</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-info" onclick="viewBill({{ $bill->id }})" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($bill->status !== 'paid')
                                                            <button class="btn btn-sm btn-outline-warning" onclick="editBill({{ $bill->id }})" title="Edit Tagihan">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                        @if($bill->status !== 'paid' && !$bill->payments()->where('status', 'verified')->exists())
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBill({{ $bill->id }})" title="Hapus Tagihan">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
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
                                {{ $bills->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada tagihan</h4>
                                <p class="text-muted">Mulai dengan membuat tagihan pertama Anda.</p>
                                <a href="{{ route('admin.bills.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Buat Tagihan Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Cards -->
                @if($bills->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $bills->where('status', 'pending')->count() }}</h4>
                                        <p class="mb-0">Belum Dibayar</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $bills->where('status', 'paid')->count() }}</h4>
                                        <p class="mb-0">Sudah Dibayar</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $bills->where('due_date', '<', now())->where('status', '!=', 'paid')->count() }}</h4>
                                        <p class="mb-0">Terlambat</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">Rp {{ number_format($bills->where('status', 'paid')->sum('total_amount'), 0, ',', '.') }}</h4>
                                        <p class="mb-0">Total Terbayar</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-dollar-sign fa-2x"></i>
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
</style>

<script>
function viewBill(billId) {
    // Redirect to bill detail page
    window.location.href = `/admin/bills/${billId}`;
}

function editBill(billId) {
    // Redirect to edit bill page
    window.location.href = `/admin/bills/${billId}/edit`;
}

function deleteBill(billId) {
    if (confirm('Apakah Anda yakin ingin menghapus tagihan ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/bills/${billId}`;
        
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

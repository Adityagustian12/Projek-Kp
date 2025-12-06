@extends('layouts.app')

@section('title', 'Tagihan Saya - Dashboard Penghuni')

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
                    <a class="nav-link active" href="{{ route('tenant.bills') }}">
                        <i class="fas fa-file-invoice me-2"></i>Tagihan Saya
                    </a>
                    <a class="nav-link" href="{{ route('tenant.complaints') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Keluhan Saya
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Tagihan Saya
                    </h2>
                </div>


                <!-- Bills List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Tagihan
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($bills->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="d-none d-md-block">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No. Tagihan</th>
                                                <th>Kamar</th>
                                                <th>Periode</th>
                                                <th>Jumlah Tagihan</th>
                                                <th>Jatuh Tempo</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bills as $bill)
                                                <tr>
                                                    <td><strong>#{{ $bill->id }}</strong></td>
                                                    <td>
                                                        <strong>{{ $bill->room->room_number }}</strong>
                                                        <br>
                                                        <small class="text-muted">Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bulan</small>
                                                    </td>
                                                    <td>
                                                        <strong>{{ \Carbon\Carbon::parse($bill->period_start)->format('d M') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}</strong>
                                                    </td>
                                                    <td><strong>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</strong></td>
                                                    <td>
                                                        <strong>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</strong>
                                                        @if($bill->status === 'overdue')
                                                            <br>
                                                            @php
                                                                $daysLate = \Carbon\Carbon::parse($bill->due_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                                            @endphp
                                                            <small class="text-danger">Terlambat {{ max(0, (int) $daysLate) }} hari</small>
                                                        @elseif($bill->status === 'pending')
                                                            <br>
                                                            @php
                                                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($bill->due_date)->startOfDay(), false);
                                                            @endphp
                                                            <small class="text-warning">{{ $daysLeft > 0 ? (int) $daysLeft . ' hari lagi' : 'Hari ini' }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bill->status === 'pending')
                                                            <span class="badge bg-warning">Belum Dibayar</span>
                                                        @elseif($bill->status === 'paid')
                                                            <span class="badge bg-success">Sudah Dibayar</span>
                                                        @elseif($bill->status === 'overdue')
                                                            <span class="badge bg-danger">Terlambat</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($bill->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-info" onclick="viewBill({{ $bill->id }})" title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if($bill->status === 'pending' || $bill->status === 'overdue')
                                                                <button class="btn btn-sm btn-outline-success" onclick="payBill({{ $bill->id }})" title="Bayar Tagihan">
                                                                    <i class="fas fa-credit-card"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @foreach($bills as $bill)
                                    <div class="card mb-3 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1 text-muted">Tagihan #{{ $bill->id }}</h6>
                                                    <h5 class="mb-0">{{ $bill->room->room_number }}</h5>
                                                    <small class="text-muted">Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bulan</small>
                                                </div>
                                                <div>
                                                    @if($bill->status === 'pending')
                                                        <span class="badge bg-warning">Belum Dibayar</span>
                                                    @elseif($bill->status === 'paid')
                                                        <span class="badge bg-success">Sudah Dibayar</span>
                                                    @elseif($bill->status === 'overdue')
                                                        <span class="badge bg-danger">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($bill->status) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="border-top pt-3 mb-3">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Periode</small>
                                                        <strong>{{ \Carbon\Carbon::parse($bill->period_start)->format('d M') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}</strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Jatuh Tempo</small>
                                                        <strong>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</strong>
                                                        @if($bill->status === 'overdue')
                                                            @php
                                                                $daysLate = \Carbon\Carbon::parse($bill->due_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                                            @endphp
                                                            <br><small class="text-danger">Terlambat {{ max(0, (int) $daysLate) }} hari</small>
                                                        @elseif($bill->status === 'pending')
                                                            @php
                                                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($bill->due_date)->startOfDay(), false);
                                                            @endphp
                                                            <br><small class="text-warning">{{ $daysLeft > 0 ? (int) $daysLeft . ' hari lagi' : 'Hari ini' }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="border-top pt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted">Jumlah Tagihan</span>
                                                    <h4 class="mb-0 text-primary">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</h4>
                                                </div>
                                                
                                                <div class="d-grid gap-2 mt-3">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewBill({{ $bill->id }})">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                                    </button>
                                                    @if($bill->status === 'pending' || $bill->status === 'overdue')
                                                        <button class="btn btn-success btn-sm" onclick="payBill({{ $bill->id }})">
                                                            <i class="fas fa-credit-card me-2"></i>Bayar Tagihan
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bills->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada tagihan</h4>
                                <p class="text-muted">Belum ada tagihan yang perlu dibayar.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Cards -->
                @if($bills->count() > 0)
                <div class="row g-3 mt-3">
                    <div class="col-6 col-md-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1">{{ $bills->where('status', 'pending')->count() }}</h3>
                                        <small class="d-block">Belum Dibayar</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1">{{ $bills->where('status', 'overdue')->count() }}</h3>
                                        <small class="d-block">Terlambat</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1">{{ $bills->where('status', 'paid')->count() }}</h3>
                                        <small class="d-block">Sudah Dibayar</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-check fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1" style="font-size: 0.9rem;">Rp {{ number_format($bills->whereIn('status', ['pending', 'overdue'])->sum('total_amount'), 0, ',', '.') }}</h6>
                                        <small class="d-block">Total Belum Dibayar</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-money-bill-wave fa-lg"></i>
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

/* Mobile Responsive */
@media (max-width: 767px) {
    .main-content {
        padding: 1rem !important;
    }
    
    .card-header h5 {
        font-size: 1rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    /* Summary cards mobile */
    .card.bg-warning,
    .card.bg-danger,
    .card.bg-success,
    .card.bg-primary {
        margin-bottom: 0.5rem;
    }
    
    .card.bg-warning h3,
    .card.bg-danger h3,
    .card.bg-success h3 {
        font-size: 1.5rem !important;
    }
    
    .card.bg-primary h6 {
        font-size: 0.85rem !important;
    }
    
    /* Bill card mobile */
    .card.mb-3 {
        border: 1px solid #dee2e6;
    }
    
    .card.mb-3 .card-body {
        padding: 1rem !important;
    }
    
    .card.mb-3 h5 {
        font-size: 1.1rem !important;
    }
    
    .card.mb-3 h4 {
        font-size: 1.2rem !important;
    }
    
    .card.mb-3 .btn {
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem !important;
    }
}
</style>

<script>
function viewBill(billId) {
    // Redirect to bill detail page
    window.location.href = `/tenant/bills/${billId}`;
}

function payBill(billId) {
    // Redirect to payment form
    window.location.href = `/tenant/bills/${billId}/payment`;
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

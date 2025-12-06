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
                            <!-- Compact Table View for Desktop (≥992px) - All columns visible without scroll -->
                            <div class="d-none d-md-block">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 80px;">#</th>
                                                <th style="min-width: 120px;">Kamar</th>
                                                <th style="min-width: 140px;">Periode</th>
                                                <th style="min-width: 120px;">Jumlah</th>
                                                <th style="min-width: 120px;">Jatuh Tempo</th>
                                                <th style="min-width: 100px;">Status</th>
                                                <th style="min-width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bills as $bill)
                                                <tr>
                                                    <td><strong>#{{ $bill->id }}</strong></td>
                                                    <td>
                                                        <strong class="small">{{ $bill->room->room_number }}</strong>
                                                        <br>
                                                        <small class="text-muted" style="font-size: 0.75rem;">Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bln</small>
                                                    </td>
                                                    <td>
                                                        <small><strong>{{ \Carbon\Carbon::parse($bill->period_start)->format('d M') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}</strong></small>
                                                    </td>
                                                    <td><strong class="text-primary">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</strong></td>
                                                    <td>
                                                        <small><strong>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</strong></small>
                                                        @if($bill->status === 'overdue')
                                                            @php
                                                                $daysLate = \Carbon\Carbon::parse($bill->due_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                                            @endphp
                                                            <br><small class="text-danger" style="font-size: 0.7rem;">+{{ max(0, (int) $daysLate) }}hr</small>
                                                        @elseif($bill->status === 'pending')
                                                            @php
                                                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($bill->due_date)->startOfDay(), false);
                                                            @endphp
                                                            <br><small class="text-warning" style="font-size: 0.7rem;">{{ $daysLeft > 0 ? (int) $daysLeft . 'hr' : 'Hari ini' }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bill->status === 'pending')
                                                            <span class="badge bg-warning" style="font-size: 0.75rem;">Belum</span>
                                                        @elseif($bill->status === 'paid')
                                                            <span class="badge bg-success" style="font-size: 0.75rem;">Lunas</span>
                                                        @elseif($bill->status === 'overdue')
                                                            <span class="badge bg-danger" style="font-size: 0.75rem;">Telat</span>
                                                        @else
                                                            <span class="badge bg-secondary" style="font-size: 0.75rem;">{{ ucfirst($bill->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-info btn-sm" onclick="viewBill({{ $bill->id }})" title="Detail" style="padding: 0.25rem 0.5rem;">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if($bill->status === 'pending' || $bill->status === 'overdue')
                                                                <button class="btn btn-outline-success btn-sm" onclick="payBill({{ $bill->id }})" title="Bayar" style="padding: 0.25rem 0.5rem;">
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

                            <!-- Mobile Card View - For all mobile screens (<768px) -->
                            <div class="d-md-none">
                                @foreach($bills as $bill)
                                    <div class="card mb-3 shadow-sm border">
                                        <div class="card-body p-3">
                                            <!-- Header dengan Status -->
                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Tagihan #{{ $bill->id }}</h6>
                                                    <small class="text-muted">{{ $bill->room->room_number }}</small>
                                                </div>
                                                <div>
                                                    @if($bill->status === 'pending')
                                                        <span class="badge bg-warning text-dark">Belum Dibayar</span>
                                                    @elseif($bill->status === 'paid')
                                                        <span class="badge bg-success">Sudah Dibayar</span>
                                                    @elseif($bill->status === 'overdue')
                                                        <span class="badge bg-danger">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($bill->status) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Informasi Lengkap -->
                                            <div class="mb-3">
                                                <!-- Jumlah Tagihan - Paling Menonjol -->
                                                <div class="bg-light rounded p-2 mb-3 text-center">
                                                    <small class="text-muted d-block mb-1">Jumlah Tagihan</small>
                                                    <h4 class="mb-0 text-primary fw-bold">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</h4>
                                                </div>
                                                
                                                <!-- Detail Informasi -->
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                                            <span class="text-muted">Kamar</span>
                                                            <strong>{{ $bill->room->room_number }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                                            <span class="text-muted">Harga Kamar</span>
                                                            <strong>Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bulan</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                                            <span class="text-muted">Periode</span>
                                                            <strong>{{ \Carbon\Carbon::parse($bill->period_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                                            <span class="text-muted">Jatuh Tempo</span>
                                                            <div class="text-end">
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
                                                                    <br><small class="text-warning">{{ $daysLeft > 0 ? (int) $daysLeft . ' hari lagi' : 'Hari ini jatuh tempo' }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if($bill->late_fee > 0)
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                                            <span class="text-muted">Denda Keterlambatan</span>
                                                            <strong class="text-danger">Rp {{ number_format($bill->late_fee, 0, ',', '.') }}</strong>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if($bill->paid_at)
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between py-2">
                                                            <span class="text-muted">Tanggal Dibayar</span>
                                                            <strong>{{ \Carbon\Carbon::parse($bill->paid_at)->format('d M Y H:i') }}</strong>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Tombol Aksi - Selalu Terlihat, Tidak Perlu Scroll -->
                                            <div class="d-grid gap-2 mt-3 pt-3 border-top">
                                                <button class="btn btn-outline-primary btn-block" onclick="viewBill({{ $bill->id }})">
                                                    <i class="fas fa-eye me-2"></i>Lihat Detail Lengkap
                                                </button>
                                                @if($bill->status === 'pending' || $bill->status === 'overdue')
                                                    <button class="btn btn-success btn-block" onclick="payBill({{ $bill->id }})">
                                                        <i class="fas fa-credit-card me-2"></i>Bayar Tagihan Sekarang
                                                    </button>
                                                @endif
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

/* Mobile Responsive - Force card view, NO table */
@media screen and (max-width: 767px) {
    /* Hide ALL tables on mobile - AGGRESSIVE */
    .card-body .table-responsive,
    .card-body .table,
    .card-body table,
    .card-body thead,
    .card-body tbody,
    .card-body tr,
    .card-body th,
    .card-body td,
    .d-none.d-md-block,
    .d-none.d-lg-block,
    .d-none.d-xl-block {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
    }
    
    /* Force card view to be visible */
    .d-md-none {
        display: block !important;
        visibility: visible !important;
        width: 100% !important;
        position: relative !important;
    }
    
    /* Prevent any horizontal overflow */
    .card-body,
    .container-fluid,
    .main-content,
    .card,
    .row,
    [class*="col-"] {
        overflow-x: hidden !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw !important;
        width: 100% !important;
    }
    
    /* Ensure no element causes horizontal scroll */
    * {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
}

/* Desktop table - make it compact */
@media screen and (min-width: 768px) {
    .table-sm th,
    .table-sm td {
        padding: 0.5rem 0.25rem !important;
        font-size: 0.875rem !important;
    }
    
    .table-sm th {
        font-size: 0.8rem !important;
        white-space: nowrap;
    }
    
    /* Ensure table fits without horizontal scroll */
    .table-responsive {
        overflow-x: visible !important;
    }
    
    .table {
        width: 100% !important;
        table-layout: auto !important;
    }
}
    
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
    
    /* Bill card mobile - Full width, no scroll */
    .card.mb-3 {
        border: 1px solid #dee2e6;
        width: 100%;
        max-width: 100%;
        overflow: visible !important;
    }
    
    .card.mb-3 .card-body {
        padding: 1rem !important;
        width: 100%;
        overflow: visible !important;
    }
    
    .card.mb-3 h5 {
        font-size: 1.1rem !important;
    }
    
    .card.mb-3 h4 {
        font-size: 1.3rem !important;
    }
    
    .card.mb-3 h6 {
        font-size: 0.95rem !important;
    }
    
    .card.mb-3 .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .card.mb-3 .btn {
        font-size: 0.875rem !important;
        padding: 0.6rem 1rem !important;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .card.mb-3 .btn:last-child {
        margin-bottom: 0;
    }
    
    .card.mb-3 .d-flex.justify-content-between {
        font-size: 0.9rem;
    }
    
    .card.mb-3 strong {
        font-size: 0.95rem;
    }
    
    /* Ensure no horizontal overflow */
    .container-fluid {
        overflow-x: hidden !important;
        max-width: 100% !important;
    }
    
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    [class*="col-"] {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
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

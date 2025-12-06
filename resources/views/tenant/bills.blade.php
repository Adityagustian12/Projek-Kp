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
                            <!-- Desktop Table View - Only for screens ≥992px (desktop only) -->
                            <div class="d-none d-lg-block bills-table-desktop" style="display: none !important;">
                                <div class="table-responsive" style="display: none !important;">
                                    <table class="table table-hover table-sm" style="display: none !important;">
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

                            <!-- Mobile Card View - For all mobile screens (<992px) - NO TABLE -->
                            <!-- Works for both portrait and landscape orientation -->
                            <div class="d-lg-none bills-card-mobile" style="display: block !important; width: 100% !important; max-width: 100% !important;">
                                @foreach($bills as $bill)
                                    <div class="card mb-3 shadow-sm border">
                                        <div class="card-body p-3">
                                            <!-- Header dengan Status dan Tombol Aksi di Atas -->
                                            <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">Tagihan #{{ $bill->id }}</h6>
                                                    <small class="text-muted d-block">{{ $bill->room->room_number }}</small>
                                                    @if($bill->status === 'pending')
                                                        <span class="badge bg-warning text-dark mt-1">Belum Dibayar</span>
                                                    @elseif($bill->status === 'paid')
                                                        <span class="badge bg-success mt-1">Sudah Dibayar</span>
                                                    @elseif($bill->status === 'overdue')
                                                        <span class="badge bg-danger mt-1">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-secondary mt-1">{{ ucfirst($bill->status) }}</span>
                                                    @endif
                                                </div>
                                                <!-- Tombol Aksi di Samping Header - Langsung Terlihat -->
                                                <div class="ms-2 d-flex flex-column gap-1">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewBill({{ $bill->id }})" title="Detail" style="white-space: nowrap; min-width: auto;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($bill->status === 'pending' || $bill->status === 'overdue')
                                                        <button class="btn btn-sm btn-success" onclick="payBill({{ $bill->id }})" title="Bayar" style="white-space: nowrap; min-width: auto;">
                                                            <i class="fas fa-credit-card"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Informasi Lengkap -->
                                            <div class="mb-2">
                                                <!-- Jumlah Tagihan - Paling Menonjol -->
                                                <div class="bg-light rounded p-2 mb-2 text-center">
                                                    <small class="text-muted d-block mb-1">Jumlah Tagihan</small>
                                                    <h5 class="mb-0 text-primary fw-bold">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</h5>
                                                </div>
                                                
                                                <!-- Detail Informasi - Compact -->
                                                <div class="small">
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <span class="text-muted">Kamar:</span>
                                                        <strong>{{ $bill->room->room_number }}</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <span class="text-muted">Harga:</span>
                                                        <strong>Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bln</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <span class="text-muted">Periode:</span>
                                                        <strong class="text-end" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($bill->period_start)->format('d M') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between py-1">
                                                        <span class="text-muted">Jatuh Tempo:</span>
                                                        <div class="text-end">
                                                            <strong style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</strong>
                                                            @if($bill->status === 'overdue')
                                                                @php
                                                                    $daysLate = \Carbon\Carbon::parse($bill->due_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                                                @endphp
                                                                <br><small class="text-danger" style="font-size: 0.75rem;">+{{ max(0, (int) $daysLate) }}hr</small>
                                                            @elseif($bill->status === 'pending')
                                                                @php
                                                                    $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($bill->due_date)->startOfDay(), false);
                                                                @endphp
                                                                <br><small class="text-warning" style="font-size: 0.75rem;">{{ $daysLeft > 0 ? (int) $daysLeft . 'hr' : 'Hari ini' }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($bill->late_fee > 0)
                                                    <div class="d-flex justify-content-between py-1 border-top">
                                                        <span class="text-muted">Denda:</span>
                                                        <strong class="text-danger">Rp {{ number_format($bill->late_fee, 0, ',', '.') }}</strong>
                                                    </div>
                                                    @endif
                                                    @if($bill->paid_at)
                                                    <div class="d-flex justify-content-between py-1 border-top">
                                                        <span class="text-muted">Dibayar:</span>
                                                        <strong style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($bill->paid_at)->format('d M Y H:i') }}</strong>
                                                    </div>
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

/* Mobile & Tablet Responsive - Force card view, NO table - Works for portrait and landscape */
/* Portrait mode - Force card view - NO SCROLL */
@media screen and (max-width: 991px) and (orientation: portrait) {
    /* Hide ALL tables on mobile portrait - ULTRA AGGRESSIVE */
    .bills-table-desktop,
    .bills-table-desktop *,
    .table-responsive,
    .table,
    table,
    thead,
    tbody,
    tr,
    th,
    td,
    .d-none.d-lg-block,
    .d-none.d-md-block {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        opacity: 0 !important;
    }
    
    /* Force card view to be visible */
    .bills-card-mobile,
    .d-lg-none {
        display: block !important;
        visibility: visible !important;
        width: 100% !important;
        max-width: 100% !important;
        position: relative !important;
        opacity: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Prevent any horizontal overflow - AGGRESSIVE */
    .card-body,
    .container-fluid,
    .main-content,
    .card,
    .row,
    [class*="col-"],
    .card-body > *,
    .bills-card-mobile > *,
    .bills-card-mobile .card,
    .bills-card-mobile .card .card-body {
        overflow-x: hidden !important;
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw !important;
        width: 100% !important;
        position: relative !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Ensure no element causes horizontal scroll */
    * {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    /* Force card to be full width with proper padding */
    .bills-card-mobile .card {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    .bills-card-mobile .card .card-body {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* Ensure buttons don't cause overflow */
    .bills-card-mobile .btn {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    /* Ensure all text wraps */
    .bills-card-mobile * {
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }
}

/* Landscape mode - Also use card view to be safe */
@media screen and (max-width: 991px) and (orientation: landscape) {
    /* Hide tables */
    .bills-table-desktop,
    .table-responsive,
    .table {
        display: none !important;
    }
    
    /* Show card view */
    .bills-card-mobile {
        display: block !important;
    }
    
    /* Prevent horizontal scroll */
    html, body {
        overflow-x: hidden !important;
    }
}

/* General mobile - below 992px */
@media screen and (max-width: 991px) {
    /* Hide ALL tables */
    .bills-table-desktop,
    .bills-table-desktop *,
    .table-responsive,
    .table,
    table {
        display: none !important;
    }
    
    /* Show card view */
    .bills-card-mobile {
        display: block !important;
        width: 100% !important;
    }
    
    /* Prevent horizontal scroll */
    html, body, .container-fluid, .main-content {
        overflow-x: hidden !important;
        max-width: 100vw !important;
    }
}

/* Extra safety for small screens in any orientation */
@media screen and (max-width: 767px) {
    /* Double check - hide any remaining tables */
    .bills-table-desktop,
    .table-responsive,
    .table,
    table {
        display: none !important;
    }
    
    /* Ensure card view */
    .bills-card-mobile {
        display: block !important;
    }
}

/* Mobile landscape orientation - Force card view */
@media screen and (max-width: 991px) and (orientation: landscape) {
    .bills-table-desktop,
    .table-responsive,
    .table {
        display: none !important;
    }
    
    .bills-card-mobile {
        display: block !important;
    }
}

/* Mobile portrait orientation - Force card view */
@media screen and (max-width: 991px) and (orientation: portrait) {
    .bills-table-desktop,
    .table-responsive,
    .table {
        display: none !important;
    }
    
    .bills-card-mobile {
        display: block !important;
    }
}

/* Desktop table - make it compact (only for screens ≥992px) */
@media screen and (min-width: 992px) {
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

/* Tablet landscape - still use card view to avoid scroll */
@media screen and (min-width: 768px) and (max-width: 991px) {
    /* Hide table on tablet */
    .bills-table-desktop,
    .table-responsive,
    .table {
        display: none !important;
    }
    
    /* Show card view */
    .bills-card-mobile {
        display: block !important;
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
    
    /* Bill card mobile - Full width, no scroll, compact layout */
    .card.mb-3 {
        border: 1px solid #dee2e6;
        width: 100%;
        max-width: 100%;
        overflow: visible !important;
    }
    
    .card.mb-3 .card-body {
        padding: 0.75rem !important;
        width: 100%;
        overflow: visible !important;
    }
    
    .card.mb-3 h5 {
        font-size: 1.1rem !important;
    }
    
    .card.mb-3 h6 {
        font-size: 0.9rem !important;
        margin-bottom: 0.25rem !important;
    }
    
    .card.mb-3 .bg-light {
        background-color: #f8f9fa !important;
        padding: 0.5rem !important;
    }
    
    .card.mb-3 .bg-light h5 {
        font-size: 1rem !important;
    }
    
    /* Tombol aksi di header - compact */
    .card.mb-3 .btn-sm {
        font-size: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
        min-width: 36px !important;
    }
    
    /* Detail informasi - compact */
    .card.mb-3 .small {
        font-size: 0.8rem !important;
    }
    
    .card.mb-3 .d-flex.justify-content-between {
        font-size: 0.8rem !important;
        padding: 0.25rem 0 !important;
    }
    
    .card.mb-3 strong {
        font-size: 0.85rem !important;
    }
    
    .card.mb-3 .text-muted {
        font-size: 0.75rem !important;
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

// Force hide table on mobile - Run immediately (works for both portrait and landscape)
(function() {
    function hideTableOnMobile() {
        // Use 992px breakpoint to match Bootstrap lg breakpoint
        const isMobile = window.innerWidth < 992;
        const isPortrait = window.innerHeight > window.innerWidth;
        
        if (isMobile) {
            // Hide all tables - ULTRA AGGRESSIVE
            const tables = document.querySelectorAll('.bills-table-desktop, .table-responsive, .table, table, thead, tbody');
            tables.forEach(function(table) {
                if (table) {
                    table.style.setProperty('display', 'none', 'important');
                    table.style.setProperty('visibility', 'hidden', 'important');
                    table.style.setProperty('width', '0', 'important');
                    table.style.setProperty('height', '0', 'important');
                    table.style.setProperty('position', 'absolute', 'important');
                    table.style.setProperty('left', '-9999px', 'important');
                    table.style.setProperty('opacity', '0', 'important');
                    table.classList.add('d-none');
                }
            });
            
            // Show card view
            const cardView = document.querySelector('.bills-card-mobile');
            if (cardView) {
                cardView.style.setProperty('display', 'block', 'important');
                cardView.style.setProperty('visibility', 'visible', 'important');
                cardView.style.setProperty('width', '100%', 'important');
                cardView.style.setProperty('max-width', '100%', 'important');
                cardView.classList.remove('d-none');
            }
            
            // Prevent horizontal scroll
            document.body.style.setProperty('overflow-x', 'hidden', 'important');
            document.documentElement.style.setProperty('overflow-x', 'hidden', 'important');
            document.body.style.setProperty('max-width', '100vw', 'important');
            document.documentElement.style.setProperty('max-width', '100vw', 'important');
            
            // Hide any table-responsive containers
            const tableResponsive = document.querySelectorAll('.table-responsive');
            tableResponsive.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });
        } else {
            // Show table on desktop (≥992px)
            const tables = document.querySelectorAll('.bills-table-desktop');
            tables.forEach(function(table) {
                if (table) {
                    table.style.display = '';
                    table.style.visibility = 'visible';
                    table.style.position = 'relative';
                    table.style.left = '0';
                    table.style.opacity = '1';
                }
            });
            
            // Hide card view on desktop
            const cardView = document.querySelector('.bills-card-mobile');
            if (cardView) {
                cardView.style.display = 'none';
            }
        }
    }
    
    // Run immediately
    hideTableOnMobile();
    
    // Run on load
    window.addEventListener('load', hideTableOnMobile);
    
    // Run on resize and orientation change
    window.addEventListener('resize', hideTableOnMobile);
    window.addEventListener('orientationchange', function() {
        setTimeout(hideTableOnMobile, 100);
    });
    
    // Run after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hideTableOnMobile);
    } else {
        setTimeout(hideTableOnMobile, 50);
    }
    
    // Run multiple times to ensure it works
    setTimeout(hideTableOnMobile, 100);
    setTimeout(hideTableOnMobile, 300);
    setTimeout(hideTableOnMobile, 500);
})();

// Show success/error messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endsection

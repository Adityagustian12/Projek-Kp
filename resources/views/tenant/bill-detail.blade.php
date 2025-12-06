@extends('layouts.app')

@section('title', 'Detail Tagihan - Dashboard Penghuni')

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
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Detail Tagihan #{{ $bill->id }}
                        </h2>
                        
                    </div>
                    <div>
                        <a href="{{ route('tenant.bills') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        @if($bill->status === 'pending' || $bill->status === 'overdue')
                            <a href="{{ route('tenant.bills.payment', $bill) }}" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i>Bayar Tagihan
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <!-- Bill Information -->
                    <div class="col-lg-8">
                        <!-- Basic Info Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Tagihan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nomor Tagihan:</strong>
                                        <p class="text-muted mb-0">#{{ $bill->id }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Status:</strong>
                                        <p class="text-muted mb-0">
                                            @if($bill->status === 'pending')
                                                <span class="badge bg-warning">Belum Dibayar</span>
                                            @elseif($bill->status === 'paid')
                                                <span class="badge bg-success">Sudah Dibayar</span>
                                            @elseif($bill->status === 'overdue')
                                                <span class="badge bg-danger">Terlambat</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($bill->status) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Periode Tagihan:</strong>
                                        <p class="text-muted mb-0">
                                            @if($bill->period_start && $bill->period_end)
                                                {{ \Carbon\Carbon::parse($bill->period_start)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}
                                            @else
                                                {{ \Carbon\Carbon::create($bill->year, $bill->month, 1)->format('M Y') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Tanggal Jatuh Tempo:</strong>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Tanggal Dibuat:</strong>
                                        <p class="text-muted mb-0">{{ $bill->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    @if($bill->paid_at)
                                        <div class="col-md-6 mb-3">
                                            <strong>Tanggal Dibayar:</strong>
                                            <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($bill->paid_at)->format('d M Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bill Breakdown Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>Rincian Tagihan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td><strong>Sewa Kamar:</strong></td>
                                                <td class="text-end">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Total Tagihan:</strong></td>
                                                <td class="text-end"><strong>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Room Information Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bed me-2"></i>Informasi Kamar
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nomor Kamar:</strong>
                                        <p class="text-muted mb-0">{{ $bill->room->room_number }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Harga Sewa:</strong>
                                        <p class="text-muted mb-0">Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bulan</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Kapasitas:</strong>
                                        <p class="text-muted mb-0">{{ $bill->room->capacity }} orang</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Status Kamar:</strong>
                                        <p class="text-muted mb-0">
                                            <span class="badge bg-{{ $bill->room->status === 'available' ? 'success' : ($bill->room->status === 'occupied' ? 'warning' : 'danger') }}">
                                                {{ $bill->room->status === 'available' ? 'Tersedia' : ($bill->room->status === 'occupied' ? 'Terisi' : 'Maintenance') }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                @if($bill->room->description)
                                    <div class="mt-3">
                                        <strong>Deskripsi Kamar:</strong>
                                        <p class="text-muted">{{ $bill->room->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">

                        <!-- Payment Status -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Status Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($bill->status === 'paid')
                                    <div class="text-center">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5 class="text-success">Sudah Dibayar</h5>
                                        <p class="text-muted mb-0">
                                            Dibayar pada {{ \Carbon\Carbon::parse($bill->paid_at)->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                @elseif($bill->status === 'overdue')
                                    <div class="text-center">
                                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                        <h5 class="text-danger">Terlambat</h5>
                                        @php
                                            $daysLate = \Carbon\Carbon::parse($bill->due_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                        @endphp
                                        <p class="text-muted mb-0">
                                            Terlambat {{ max(0, (int) $daysLate) }} hari
                                        </p>
                                        @if(isset($bill->late_fee) && $bill->late_fee > 0)
                                            <p class="text-danger mb-0">
                                                Denda: Rp {{ number_format($bill->late_fee, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                        <h5 class="text-warning">Belum Dibayar</h5>
                                        <p class="text-muted mb-0">
                                            Jatuh tempo: {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                        </p>
                                        @php
                                            $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($bill->due_date)->startOfDay(), false);
                                        @endphp
                                        <p class="text-warning mb-0">
                                            {{ max(0, (int) $daysLeft) }} hari lagi
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Payment History -->
                        @if($bill->payments && $bill->payments->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Riwayat Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($bill->payments as $payment)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-1">Rp {{ number_format($payment->amount, 0, ',', '.') }}</h6>
                                            <small class="text-muted">{{ $payment->created_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <div>
                                            @if($payment->status === 'verified')
                                                <span class="badge bg-success">Verified</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
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

.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}
</style>

<script>
function printBill() {
    // Implementasi print bill
    window.print();
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

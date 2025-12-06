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
                                            <th>Kamar</th>
                                            <th>Periode</th>
                                            <th>Jumlah Tagihan</th>
                                            <th>Tanggal Jatuh Tempo</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bills as $bill)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $bill->id }}</strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $bill->room->room_number }}</strong>
                                                        <br>
                                                        <small class="text-muted">Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bulan</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ \Carbon\Carbon::parse($bill->period_start)->format('M Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($bill->period_start)->format('d M') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d M Y') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</strong>
                                                        @if($bill->status === 'overdue')
                                                            <br>
                                                            @php
                                                                $daysLate = \Carbon\Carbon::parse($bill->due_date)->startOfDay()->diffInDays(now()->startOfDay(), false);
                                                            @endphp
                                                            <small class="text-danger">
                                                                Terlambat {{ max(0, (int) $daysLate) }} hari
                                                            </small>
                                                        @elseif($bill->status === 'pending')
                                                            <br>
                                                            @php
                                                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($bill->due_date)->startOfDay(), false);
                                                            @endphp
                                                            <small class="text-warning">
                                                                {{ $daysLeft > 0 ? (int) $daysLeft . ' hari lagi' : 'Hari ini jatuh tempo' }}
                                                            </small>
                                                        @endif
                                                    </div>
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
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
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
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $bills->where('status', 'overdue')->count() }}</h4>
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
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">Rp {{ number_format($bills->whereIn('status', ['pending', 'overdue'])->sum('total_amount'), 0, ',', '.') }}</h4>
                                        <p class="mb-0">Total Belum Dibayar</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-money-bill-wave fa-2x"></i>
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

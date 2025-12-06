@extends('layouts.app')

@section('title', 'Dashboard Penghuni - Kos-Kosan H.Kastim')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
            <div class="p-3">
                <h6 class="text-muted text-uppercase mb-3">Menu Penghuni</h6>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('tenant.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('tenant.bills') }}">
                        <i class="fas fa-file-invoice me-2"></i>Tagihan
                    </a>
                    <a class="nav-link" href="{{ route('tenant.complaints') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Komplain
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Penghuni
                    </h2>
                    <span class="badge bg-success fs-6">Selamat datang, {{ auth()->user()->name }}</span>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['pending_bills'] }}</h4>
                                        <p class="mb-0">Tagihan Belum Lunas</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file-invoice fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['overdue_bills'] }}</h4>
                                        <p class="mb-0">Tagihan Jatuh Tempo</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['active_complaints'] }}</h4>
                                        <p class="mb-0">Komplain Aktif</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
<div>
                                        <h4 class="mb-0">{{ $stats['pending_payments'] }}</h4>
                                        <p class="mb-0">Pembayaran Menunggu</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-credit-card fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>Tagihan Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                @forelse($recent_bills as $bill)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-file-invoice-dollar fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Tagihan {{ $bill->month }}/{{ $bill->year }}</h6>
                                            <p class="mb-1 text-muted">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</p>
                                            <small class="text-muted">Jatuh tempo: {{ $bill->due_date->format('d M Y') }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($bill->status === 'paid')
                                                <span class="badge bg-success">Lunas</span>
                                            @elseif($bill->status === 'overdue')
                                                <span class="badge bg-danger">Jatuh Tempo</span>
                                            @else
                                                <span class="badge bg-warning">Belum Lunas</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">Tidak ada tagihan</p>
                                @endforelse
                                
                                @if($recent_bills->count() > 0)
                                    <div class="text-center">
                                        <a href="{{ route('tenant.bills') }}" class="btn btn-outline-primary btn-sm">
                                            Lihat Semua Tagihan
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Komplain Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                @forelse($recent_complaints as $complaint)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $complaint->title }}</h6>
                                            <p class="mb-1 text-muted">{{ $complaint->category }}</p>
                                            <small class="text-muted">{{ $complaint->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $complaint->status === 'new' ? 'danger' : ($complaint->status === 'in_progress' ? 'warning' : 'success') }}">
                                                {{ $complaint->getStatusLabel() }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">Tidak ada komplain</p>
                                @endforelse
                                
                                @if($recent_complaints->count() > 0)
                                    <div class="text-center">
                                        <a href="{{ route('tenant.complaints') }}" class="btn btn-outline-warning btn-sm">
                                            Lihat Semua Komplain
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
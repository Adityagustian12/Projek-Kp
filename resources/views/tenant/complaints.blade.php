@extends('layouts.app')

@section('title', 'Keluhan Saya - Dashboard Penghuni')

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
                    <a class="nav-link" href="{{ route('tenant.bills') }}">
                        <i class="fas fa-file-invoice me-2"></i>Tagihan Saya
                    </a>
                    <a class="nav-link active" href="{{ route('tenant.complaints') }}">
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
                        <i class="fas fa-exclamation-triangle me-2"></i>Keluhan Saya
                    </h2>
                    <div>
                        <a href="{{ route('tenant.complaints.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ajukan Keluhan Baru
                        </a>
                    </div>
                </div>


                <!-- Complaints Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Keluhan Saya
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($complaints->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Keluhan</th>
                                            <th>Judul</th>
                                            
                                            
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($complaints as $complaint)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $complaint->id }}</strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ Str::limit($complaint->title, 40) }}</strong>
                                                        @if($complaint->description)
                                                            <br>
                                                            <small class="text-muted">{{ Str::limit($complaint->description, 60) }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                
                                                
                                                <td>
                                                    @if($complaint->status === 'new')
                                                        <span class="badge bg-primary">Baru</span>
                                                    @elseif($complaint->status === 'in_progress')
                                                        <span class="badge bg-warning">Sedang Diproses</span>
                                                    @elseif($complaint->status === 'resolved')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @else
                                                        <span class="badge bg-secondary">Ditutup</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $complaint->created_at->format('d M Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $complaint->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('tenant.complaints.detail', $complaint->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($complaint->status === 'new')
                                                            <a href="{{ route('tenant.complaints.detail', $complaint->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Keluhan">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
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
                                {{ $complaints->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada keluhan</h4>
                                <p class="text-muted">Anda belum mengajukan keluhan apapun.</p>
                                <a href="{{ route('tenant.complaints.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajukan Keluhan Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Cards -->
                @if(isset($stats) && ($stats['new'] > 0 || $stats['in_progress'] > 0 || $stats['resolved'] > 0))
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['new'] }}</h4>
                                        <p class="mb-0">Keluhan Baru</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['in_progress'] }}</h4>
                                        <p class="mb-0">Sedang Diproses</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['resolved'] }}</h4>
                                        <p class="mb-0">Selesai</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check fa-2x"></i>
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

.card-hover {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.status-badge {
    font-size: 0.875rem;
    padding: 0.35em 0.65em;
}
</style>

<script>
// Show success/error messages dengan toast notification yang lebih baik
@if(session('success'))
    // Buat toast notification
    const toast = document.createElement('div');
    toast.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
@endif

@if(session('error'))
    const toast = document.createElement('div');
    toast.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
@endif
</script>
@endsection

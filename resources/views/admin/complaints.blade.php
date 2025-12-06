@extends('layouts.app')

@section('title', 'Kelola Keluhan - Admin Dashboard')

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
                    <a class="nav-link active" href="{{ route('admin.complaints') }}">
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
                        <i class="fas fa-exclamation-triangle me-2"></i>Kelola Keluhan
                    </h2>
                </div>

                <!-- Complaints Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Keluhan
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($complaints->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Keluhan</th>
                                            <th>Penghuni</th>
                                            <th>Kamar</th>
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
                                                        <strong>{{ $complaint->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $complaint->user->email }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $currentRoom = $complaint->user->getCurrentRoom();
                                                    @endphp
                                                    @if($currentRoom)
                                                        <span class="badge bg-info">{{ $currentRoom->room_number }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">Tidak Menempati Kamar</span>
                                                    @endif
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
                                                        <button class="btn btn-sm btn-outline-info" onclick="viewComplaint({{ $complaint->id }})" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteComplaint({{ $complaint->id }})" title="Hapus Keluhan">
                                                            <i class="fas fa-trash"></i>
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
                                {{ $complaints->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada keluhan</h4>
                                <p class="text-muted">Belum ada keluhan yang perlu ditangani.</p>
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

</style>

<script>
function viewComplaint(complaintId) {
    // Redirect to complaint detail page
    window.location.href = `/admin/complaints/${complaintId}`;
}

function deleteComplaint(complaintId) {
    if (confirm('Apakah Anda yakin ingin menghapus keluhan ini? Tindakan ini tidak dapat dibatalkan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/complaints/${complaintId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

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

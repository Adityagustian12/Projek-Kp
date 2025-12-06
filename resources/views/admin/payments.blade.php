@extends('layouts.app')

@section('title', 'Kelola Pembayaran - Admin Dashboard')

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
                    <a class="nav-link active" href="{{ route('admin.payments') }}">
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
                        <i class="fas fa-credit-card me-2"></i>Kelola Pembayaran
                    </h2>
                </div>


                <!-- Payments Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Pembayaran</th>
                                            <th>Penghuni</th>
                                            <th>Jumlah</th>
                                            <th>Metode</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $payment->id }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $payment->created_at ? $payment->created_at->format('d M Y H:i') : 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $payment->user ? $payment->user->name : 'User tidak ditemukan' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $payment->user ? $payment->user->email : 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                                </td>
                                                <td>
                                                    @if($payment->payment_method === 'bank_transfer')
                                                        <span class="badge bg-primary">Transfer Bank</span>
                                                    @elseif($payment->payment_method === 'dana')
                                                        <span class="badge bg-info">DANA</span>
                                                    @elseif($payment->payment_method === 'gopay')
                                                        <span class="badge bg-success">GoPay</span>
                                                    @elseif($payment->payment_method === 'cash')
                                                        <span class="badge bg-success">Tunai</span>
                                                    @elseif($payment->payment_method === 'e_wallet')
                                                        <span class="badge bg-info">E-Wallet</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $payment->created_at ? $payment->created_at->format('d M Y') : '-' }}
                                                </td>
                                                <td>
                                                    @if($payment->status === 'verified')
                                                        <span class="badge bg-success">Terverifikasi</span>
                                                    @elseif($payment->status === 'rejected')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @else
                                                        <span class="badge bg-warning">Menunggu Verifikasi</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-info" onclick="viewPayment({{ $payment->id }})" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
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
                                {{ $payments->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada pembayaran</h4>
                                <p class="text-muted">Belum ada pembayaran yang perlu diverifikasi.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Cards -->
                @if($payments->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $payments->where('status', 'pending')->count() }}</h4>
                                        <p class="mb-0">Menunggu Verifikasi</p>
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
                                        <h4 class="mb-0">{{ $payments->where('status', 'verified')->count() }}</h4>
                                        <p class="mb-0">Terverifikasi</p>
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
                                        <h4 class="mb-0">{{ $payments->where('status', 'rejected')->count() }}</h4>
                                        <p class="mb-0">Ditolak</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-times fa-2x"></i>
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
                                        <h4 class="mb-0">Rp {{ number_format($payments->where('status', 'verified')->sum('amount'), 0, ',', '.') }}</h4>
                                        <p class="mb-0">Total Terverifikasi</p>
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

<!-- Verify Payment Modal -->
<div class="modal fade" id="verifyPaymentModal" tabindex="-1" aria-labelledby="verifyPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyPaymentModalLabel">
                    <i class="fas fa-check me-2"></i>Verifikasi Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyPaymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Catatan Admin (Opsional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Tambahkan catatan untuk pembayaran ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Verifikasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectPaymentModalLabel">
                    <i class="fas fa-times me-2"></i>Tolak Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectPaymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Berikan alasan mengapa pembayaran ditolak..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Tolak Pembayaran
                    </button>
                </div>
            </form>
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
function viewPayment(paymentId) {
    // Redirect to payment detail page
    window.location.href = `/admin/payments/${paymentId}`;
}

function verifyPayment(paymentId) {
    // Set form action
    document.getElementById('verifyPaymentForm').action = `/admin/payments/${paymentId}/verify`;
    
    // Set status to verified
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = 'verified';
    document.getElementById('verifyPaymentForm').appendChild(statusInput);
    
    // Show modal
    const verifyModal = new bootstrap.Modal(document.getElementById('verifyPaymentModal'));
    verifyModal.show();
}

function rejectPayment(paymentId) {
    // Set form action
    document.getElementById('rejectPaymentForm').action = `/admin/payments/${paymentId}/verify`;
    
    // Set status to rejected
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = 'rejected';
    document.getElementById('rejectPaymentForm').appendChild(statusInput);
    
    // Show modal
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectPaymentModal'));
    rejectModal.show();
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

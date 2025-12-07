@extends('layouts.app')

@section('title', 'Detail Pembayaran - Admin Dashboard')

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
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Detail Pembayaran #{{ $payment->id }}
                        </h2>
                        
                    </div>
                    <div>
                        <a href="{{ route('admin.payments') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        @if($payment->status === 'pending')
                            <button class="btn btn-success me-2" onclick="verifyPayment({{ $payment->id }}, 'verified')">
                                <i class="fas fa-check me-2"></i>Verifikasi
                            </button>
                            <button class="btn btn-danger" onclick="verifyPayment({{ $payment->id }}, 'rejected')">
                                <i class="fas fa-times me-2"></i>Tolak
                            </button>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <!-- Payment Information -->
                    <div class="col-lg-8">
                        <!-- Basic Info Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nomor Pembayaran:</strong>
                                        <p class="text-muted mb-0">#{{ $payment->id }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Status:</strong>
                                        <p class="text-muted mb-0">
                                            @if($payment->status === 'pending')
                                                <span class="badge bg-warning">Menunggu Verifikasi</span>
                                            @elseif($payment->status === 'verified')
                                                <span class="badge bg-success">Diverifikasi</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Jumlah Pembayaran:</strong>
                                        <p class="text-muted mb-0">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Metode Pembayaran:</strong>
                                        <p class="text-muted mb-0">
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
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Tanggal Pembayaran:</strong>
                                        <p class="text-muted mb-0">{{ $payment->created_at ? $payment->created_at->format('d M Y H:i') : 'N/A' }}</p>
                                    </div>
                                    @if($payment->verified_at)
                                        <div class="col-md-6 mb-3">
                                            <strong>Tanggal Verifikasi:</strong>
                                            <p class="text-muted mb-0">{{ $payment->verified_at ? $payment->verified_at->format('d M Y H:i') : 'N/A' }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($payment->notes)
                                    <div class="mt-3">
                                        <strong>Catatan:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {{ $payment->notes }}
                                        </div>
                                    </div>
                                @endif

                                @if($payment->admin_notes)
                                    <div class="mt-3">
                                        <strong>Catatan Admin:</strong>
                                        <div class="mt-2 p-3 bg-primary text-white rounded">
                                            {{ $payment->admin_notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Proof Card -->
                        @if($payment->payment_proof)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-image me-2"></i>Bukti Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        @if(pathinfo($payment->payment_proof, PATHINFO_EXTENSION) === 'pdf')
                                            <div class="text-center">
                                                <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                                                <h6>Bukti Pembayaran PDF</h6>
                                                <a href="{{ storage_url($payment->payment_proof) }}" 
                                                   target="_blank" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-download me-2"></i>Download PDF
                                                </a>
                                            </div>
                                        @else
                                            <img src="{{ storage_url($payment->payment_proof) }}" 
                                                 class="img-fluid rounded" 
                                                 alt="Bukti Pembayaran"
                                                 style="height: 400px; width: 100%; object-fit: cover; cursor: pointer;"
                                                 onclick="openImageModal('{{ storage_url($payment->payment_proof) }}')">
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center h-100">
                                            <div>
                                                <h6>Informasi File:</h6>
                                                <p class="text-muted mb-2">
                                                    <strong>Tipe:</strong> {{ pathinfo($payment->payment_proof, PATHINFO_EXTENSION) === 'pdf' ? 'PDF' : 'Gambar' }}
                                                </p>
                                                <p class="text-muted mb-2">
                                                    <strong>Ukuran:</strong> {{ number_format(filesize(storage_path('app/public/' . $payment->payment_proof)) / 1024, 1) }} KB
                                                </p>
                                                <div class="mt-3">
                                                    <a href="{{ storage_url($payment->payment_proof) }}" 
                                                       target="_blank" 
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-download me-2"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">

                        <!-- User Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Info Penghuni
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($payment->user)
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>Nama:</strong> {{ $payment->user->name }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Email:</strong> {{ $payment->user->email }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Telepon:</strong> {{ $payment->user->phone ?? 'Tidak ada' }}
                                    </div>
                                    <div class="col-12">
                                        <strong>Alamat:</strong> {{ $payment->user->address ?? 'Tidak ada' }}
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">User tidak ditemukan</h6>
                                    <p class="text-muted small">User mungkin sudah dihapus dari sistem.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bill Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>Info Tagihan
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($payment->bill)
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>Nomor Tagihan:</strong> #{{ $payment->bill->id }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Kamar:</strong> {{ $payment->bill->room ? $payment->bill->room->room_number : 'Kamar tidak ditemukan' }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Periode:</strong> {{ \Carbon\Carbon::create()->month($payment->bill->month)->format('F') }} {{ $payment->bill->year }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Total Tagihan:</strong> Rp {{ number_format($payment->bill->total_amount, 0, ',', '.') }}
                                    </div>
                                    <div class="col-12">
                                        <strong>Status Tagihan:</strong> 
                                        @if($payment->bill->status === 'pending')
                                            <span class="badge bg-warning">Belum Dibayar</span>
                                        @elseif($payment->bill->status === 'paid')
                                            <span class="badge bg-success">Sudah Dibayar</span>
                                        @else
                                            <span class="badge bg-danger">Terlambat</span>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Tagihan tidak ditemukan</h6>
                                    <p class="text-muted small">Tagihan mungkin sudah dihapus dari sistem.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Verification Status -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-shield-alt me-2"></i>Status Verifikasi
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($payment->status === 'verified')
                                    <div class="text-center">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5 class="text-success">Diverifikasi</h5>
                                        <p class="text-muted mb-0">
                                            Oleh: {{ $payment->verifier->name ?? 'Admin' }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            {{ $payment->verified_at ? $payment->verified_at->format('d M Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                @elseif($payment->status === 'rejected')
                                    <div class="text-center">
                                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                        <h5 class="text-danger">Ditolak</h5>
                                        <p class="text-muted mb-0">
                                            Oleh: {{ $payment->verifier->name ?? 'Admin' }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            {{ $payment->verified_at ? $payment->verified_at->format('d M Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                        <h5 class="text-warning">Menunggu Verifikasi</h5>
                                        <p class="text-muted mb-0">
                                            Pembayaran menunggu verifikasi admin
                                        </p>
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

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Bukti Pembayaran">
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">
                    <i class="fas fa-shield-alt me-2"></i>Verifikasi Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Catatan Admin</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                  placeholder="Tambahkan catatan untuk verifikasi pembayaran (opsional)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
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

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}
</style>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

function verifyPayment(paymentId, status) {
    const action = status === 'verified' ? 'memverifikasi' : 'menolak';
    const title = status === 'verified' ? 'Verifikasi Pembayaran' : 'Tolak Pembayaran';
    
    // Update modal title
    document.getElementById('verificationModalLabel').innerHTML = `<i class="fas fa-shield-alt me-2"></i>${title}`;
    
    // Set form action
    document.getElementById('verificationForm').action = `/admin/payments/${paymentId}/verify`;
    
    // Add status field
    const statusField = document.createElement('input');
    statusField.type = 'hidden';
    statusField.name = 'status';
    statusField.value = status;
    document.getElementById('verificationForm').appendChild(statusField);
    
    // Show modal
    const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));
    verificationModal.show();
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

@extends('layouts.app')

@section('title', 'Detail Tagihan - Admin Dashboard')

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
                    <a class="nav-link active" href="{{ route('admin.bills') }}">
                        <i class="fas fa-file-invoice me-2"></i>Kelola Tagihan
                    </a>
                    <a class="nav-link" href="{{ route('admin.payments') }}">
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
                            <i class="fas fa-file-invoice me-2"></i>Detail Tagihan #{{ $bill->id }}
                        </h2>
                        
                    </div>
                    <div>
                        <a href="{{ route('admin.bills') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        @if($bill->status !== 'paid')
                            <button class="btn btn-warning me-2" onclick="editBill({{ $bill->id }})">
                                <i class="fas fa-edit me-2"></i>Edit Tagihan
                            </button>
                        @endif
                        @if($bill->status !== 'paid' && !$bill->payments()->where('status', 'verified')->exists())
                            <button class="btn btn-danger" onclick="deleteBill({{ $bill->id }})">
                                <i class="fas fa-trash me-2"></i>Hapus Tagihan
                            </button>
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
                                            {{ \Carbon\Carbon::create()->month($bill->month)->format('F') }} {{ $bill->year }}
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

                        <!-- Payment History Card -->
                        @if($bill->payments && $bill->payments->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Riwayat Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Metode</th>
                                                <th>Status</th>
                                                <th>Verifikasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bill->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
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
                                                        @if($payment->status === 'pending')
                                                            <span class="badge bg-warning">Menunggu Verifikasi</span>
                                                        @elseif($payment->status === 'verified')
                                                            <span class="badge bg-success">Diverifikasi</span>
                                                        @else
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($payment->verified_by)
                                                            <small class="text-muted">
                                                                Oleh: {{ $payment->verifier->name ?? 'Admin' }}<br>
                                                                {{ $payment->verified_at->format('d M Y H:i') }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($payment->payment_proof)
                                                                <button class="btn btn-sm btn-outline-info" onclick="viewPaymentProof('{{ storage_url($payment->payment_proof) }}')" title="Lihat Bukti">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            @endif
                                                            @if($payment->status === 'pending')
                                                                <button class="btn btn-sm btn-outline-success" onclick="verifyPayment({{ $payment->id }}, 'verified')" title="Verifikasi">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="verifyPayment({{ $payment->id }}, 'rejected')" title="Tolak">
                                                                    <i class="fas fa-times"></i>
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
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">

                        <!-- Tenant Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Info Penghuni
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($bill->user)
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>Nama:</strong> {{ $bill->user->name }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Email:</strong> {{ $bill->user->email }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Telepon:</strong> {{ $bill->user->phone ?? 'Tidak ada' }}
                                    </div>
                                    <div class="col-12">
                                        <strong>Alamat:</strong> {{ $bill->user->address ?? 'Tidak ada' }}
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

                        <!-- Room Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bed me-2"></i>Info Kamar
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($bill->room)
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>Nomor Kamar:</strong> {{ $bill->room->room_number }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Harga:</strong> Rp {{ number_format($bill->room->price, 0, ',', '.') }}/bulan
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Kapasitas:</strong> {{ $bill->room->capacity }} orang
                                    </div>
                                    <div class="col-12">
                                        <strong>Status:</strong> 
                                        <span class="badge bg-{{ $bill->room->status === 'available' ? 'success' : ($bill->room->status === 'occupied' ? 'warning' : 'danger') }}">
                                            {{ $bill->room->status === 'available' ? 'Tersedia' : ($bill->room->status === 'occupied' ? 'Terisi' : 'Maintenance') }}
                                        </span>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Kamar tidak ditemukan</h6>
                                    <p class="text-muted small">Kamar mungkin sudah dihapus dari sistem.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="card">
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
                                            {{ $daysLeft > 0 ? (int) $daysLeft . ' hari lagi' : 'Hari ini jatuh tempo' }}
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

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentProofModalLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="paymentProofImage" src="" class="img-fluid" alt="Bukti Pembayaran">
            </div>
        </div>
    </div>
</div>

<!-- Delete Bill Confirmation Modal -->
<div class="modal fade" id="deleteBillModal" tabindex="-1" aria-labelledby="deleteBillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBillModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Tagihan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                    <h5>Apakah Anda yakin ingin menghapus tagihan ini?</h5>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data terkait tagihan ini akan dihapus secara permanen.
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Nomor Tagihan:</strong><br>
                        <span class="text-muted">#{{ $bill->id }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Penghuni:</strong><br>
                        <span class="text-muted">{{ $bill->user ? $bill->user->name : 'User tidak ditemukan' }}</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <strong>Kamar:</strong><br>
                        <span class="text-muted">{{ $bill->room ? $bill->room->room_number : 'Kamar tidak ditemukan' }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Jumlah:</strong><br>
                        <span class="text-muted">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form id="deleteBillForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus Tagihan
                    </button>
                </form>
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

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script>
function editBill(billId) {
    // Implementasi edit bill
    alert('Fitur edit tagihan akan segera tersedia!');
}

function deleteBill(billId) {
    // Set the form action
    document.getElementById('deleteBillForm').action = `/admin/bills/${billId}`;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('deleteBillModal'));
    modal.show();
}

function printBill() {
    // Implementasi print bill
    window.print();
}

function viewPaymentProof(imageSrc) {
    document.getElementById('paymentProofImage').src = imageSrc;
    const modal = new bootstrap.Modal(document.getElementById('paymentProofModal'));
    modal.show();
}

function verifyPayment(paymentId, status) {
    const action = status === 'verified' ? 'memverifikasi' : 'menolak';
    if (confirm(`Apakah Anda yakin ingin ${action} pembayaran ini?`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payments/${paymentId}/verify`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add status
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;
        form.appendChild(statusField);
        
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

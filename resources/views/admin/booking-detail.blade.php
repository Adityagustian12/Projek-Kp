@extends('layouts.app')

@section('title', 'Detail Booking - Admin Dashboard')

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
                        <i class="fas fa-calendar-check me-2"></i>Detail Booking #{{ $booking->id }}
                    </h2>
                    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Booking Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Booking
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">ID Booking</label>
                                        <p class="mb-0">#{{ $booking->id }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="mb-0">
                                            @switch($booking->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-info">Dikonfirmasi</span>
                                                    @break
                                                @case('occupied')
                                                    <span class="badge bg-success">Ditempati</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-secondary">Dibatalkan</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-dark">Selesai</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                            @endswitch
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tanggal Masuk</label>
                                        <p class="mb-0">{{ $booking->check_in_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tanggal Booking</label>
                                        <p class="mb-0">{{ $booking->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    @if($booking->notes)
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Catatan Pemesan</label>
                                            <p class="mb-0">{{ $booking->notes }}</p>
                                        </div>
                                    @endif
                                    @if($booking->admin_notes)
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Catatan Admin</label>
                                            <p class="mb-0">{{ $booking->admin_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Room Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bed me-2"></i>Informasi Kamar
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nomor Kamar</label>
                                        <p class="mb-0">{{ $booking->room ? $booking->room->room_number : 'Kamar tidak ditemukan' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Harga per Bulan</label>
                                        <p class="mb-0">Rp {{ $booking->room ? number_format($booking->room->price, 0, ',', '.') : 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Kapasitas</label>
                                        <p class="mb-0">{{ $booking->room ? $booking->room->capacity : 0 }} Orang</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Status Kamar</label>
                                        <p class="mb-0">
                                            @if($booking->room)
                                            <span class="badge bg-{{ $booking->room->status === 'available' ? 'success' : ($booking->room->status === 'occupied' ? 'warning' : 'danger') }}">
                                                {{ [
                                                    'available' => 'Tersedia',
                                                    'occupied' => 'Terisi',
                                                    'maintenance' => 'Perawatan',
                                                ][$booking->room->status] ?? ucfirst($booking->room->status) }}
                                            </span>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if($booking->room && $booking->room->description)
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">Deskripsi</label>
                                        <p class="mb-0">{{ $booking->room->description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- DP Payment Information -->
                        @php
                            $roomPrice = $booking->room ? $booking->room->price : 0;
                            $paidDp = $booking->dp_amount ?? 0;
                            $remainingDp = max(0, $roomPrice - $paidDp);
                            $isDpComplete = $paidDp >= $roomPrice && $roomPrice > 0;
                        @endphp
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>Informasi Pembayaran DP
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($roomPrice > 0)
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Harga Kosan per Bulan</label>
                                            <p class="mb-0">
                                                <strong class="text-success">Rp {{ number_format($roomPrice, 0, ',', '.') }}</strong>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">DP yang Sudah Dibayar</label>
                                            <p class="mb-0">
                                                @if($paidDp > 0)
                                                    <strong class="text-primary">Rp {{ number_format($paidDp, 0, ',', '.') }}</strong>
                                                @else
                                                    <span class="text-muted">Belum ada</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Sisa yang Harus Dibayar</label>
                                            <p class="mb-0">
                                                @if($remainingDp > 0)
                                                    <strong class="text-danger">Rp {{ number_format($remainingDp, 0, ',', '.') }}</strong>
                                                @else
                                                    <strong class="text-success">Rp 0 (Lunas)</strong>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Status DP</label>
                                            <p class="mb-0">
                                                @if($isDpComplete)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>DP Lunas
                                                    </span>
                                                @elseif($paidDp > 0)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>DP Sebagian
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Belum Bayar DP
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($remainingDp > 0 && $booking->status === 'pending')
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Perhatian:</strong> Calon penghuni masih harus melunasi sisa DP sebesar <strong>Rp {{ number_format($remainingDp, 0, ',', '.') }}</strong> sebelum booking dapat dikonfirmasi.
                                        </div>
                                    @elseif($isDpComplete)
                                        <div class="alert alert-success mb-0">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>DP sudah lunas!</strong> Booking dapat dikonfirmasi.
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informasi harga kamar tidak tersedia.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Information & Actions -->
                    <div class="col-lg-4">
                        <!-- User Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Informasi Pemesan
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($booking->user)
                                <div class="text-center mb-3">
                                    @if($booking->user->profile_picture)
                                        <img src="{{ storage_url($booking->user->profile_picture) }}" 
                                             alt="Profile Picture" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <h6 class="mb-1">{{ $booking->user->name }}</h6>
                                    <p class="text-muted mb-2">{{ $booking->user->email }}</p>
                                    @php
                                        $roleLabel = [
                                            'admin' => 'Admin',
                                            'tenant' => 'Penghuni',
                                            'seeker' => 'Pencari',
                                        ][$booking->user->role] ?? ucfirst($booking->user->role);
                                    @endphp
                                    <span class="badge bg-{{ $booking->user->role === 'admin' ? 'danger' : ($booking->user->role === 'tenant' ? 'success' : 'info') }}">{{ $roleLabel }}</span>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <small class="text-muted">Telepon</small>
                                        <p class="mb-0">{{ $booking->user->phone ?? 'Tidak ada' }}</p>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <small class="text-muted">Alamat</small>
                                        <p class="mb-0">{{ $booking->user->address ?? 'Tidak ada' }}</p>
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

                        <!-- Actions -->
                        @if($booking->status === 'pending')
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cogs me-2"></i>Aksi
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="admin_notes" class="form-label">Catatan Admin (Opsional)</label>
                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                                      placeholder="Tambahkan catatan untuk konfirmasi...">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100" 
                                                onclick="return confirm('Konfirmasi booking ini? User akan menjadi penghuni.')">
                                            <i class="fas fa-check me-2"></i>Konfirmasi Booking
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="reject_reason" class="form-label">Alasan Penolakan</label>
                                            <textarea class="form-control" id="reject_reason" name="admin_notes" rows="3" 
                                                      placeholder="Berikan alasan penolakan..." required>{{ old('admin_notes') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100" 
                                                onclick="return confirm('Tolak booking ini?')">
                                            <i class="fas fa-times me-2"></i>Tolak Booking
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Move Into Room Action -->
                        @if($booking->status === 'confirmed')
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-home me-2"></i>Pindahkan ke Kamar
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.bookings.move-in', $booking) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="move_in_notes" class="form-label">Catatan Admin (Opsional)</label>
                                            <textarea class="form-control" id="move_in_notes" name="admin_notes" rows="3" 
                                                      placeholder="Tambahkan catatan untuk pemindahan ke kamar...">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100" 
                                                onclick="return confirm('Pindahkan user ke kamar? User akan menjadi penghuni dan kamar akan terisi.')">
                                            <i class="fas fa-home me-2"></i>Pindahkan ke Kamar (User Menjadi Penghuni)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Nonaktifkan Penghuni Action -->
                        @if($booking->status === 'occupied' && $booking->user && $booking->user->role === 'tenant')
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-slash me-2"></i>Nonaktifkan Penghuni
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        Menonaktifkan penghuni akan otomatis menyelesaikan booking ini, mengembalikan kamar ke status tersedia, dan mengubah role pengguna kembali menjadi pencari kosan.
                                    </p>
                                    <form action="{{ route('admin.tenants.delete', $booking->user) }}?redirect_to=booking&booking_id={{ $booking->id }}" method="POST" id="deactivateForm" onsubmit="return confirmDeactivate()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-user-slash me-2"></i>Nonaktifkan Penghuni
                                        </button>
                                    </form>
                                    <script>
                                    function confirmDeactivate() {
                                        if (confirm('⚠️ PERINGATAN: Apakah Anda yakin ingin menonaktifkan penghuni ini?\n\nTindakan ini akan:\n• Menonaktifkan akun penghuni\n• Otomatis menyelesaikan booking ini\n• Kamar akan tersedia kembali\n• Role pengguna kembali menjadi pencari kosan\n\nKetik "NONAKTIFKAN" untuk konfirmasi:')) {
                                            const confirmation = prompt('Ketik "NONAKTIFKAN" untuk konfirmasi:');
                                            return confirmation === 'NONAKTIFKAN';
                                        }
                                        return false;
                                    }
                                    </script>
                                </div>
                            </div>
                        @endif

                        <!-- Documents -->
                        @if($booking->documents && count($booking->documents) > 0)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Dokumen Booking
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($booking->documents as $document)
                                        <div class="mb-2">
                                            <a href="{{ storage_url($document) }}" target="_blank" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Lihat Dokumen
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Payment Proof -->
                        @php
                            // Handle both old format (string) and new format (array)
                            $paymentProofs = [];
                            if ($booking->payment_proof) {
                                if (is_array($booking->payment_proof)) {
                                    // New format: array of payment proofs
                                    $paymentProofs = $booking->payment_proof;
                                } elseif (is_string($booking->payment_proof)) {
                                    // Old format: single string, try to decode as JSON first
                                    $decoded = json_decode($booking->payment_proof, true);
                                    if (is_array($decoded)) {
                                        $paymentProofs = $decoded;
                                    } else {
                                        // It's a plain string path, convert to array format
                                        $paymentProofs = [[
                                            'path' => $booking->payment_proof,
                                            'amount' => $booking->dp_amount ?? 0,
                                            'created_at' => $booking->created_at ? $booking->created_at->toDateTimeString() : now()->toDateTimeString(),
                                        ]];
                                    }
                                }
                            }
                        @endphp
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Bukti Pembayaran DP
                                    @if(count($paymentProofs) > 0)
                                        <span class="badge bg-primary ms-2">{{ count($paymentProofs) }} Bukti</span>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(count($paymentProofs) > 0)
                                    @foreach($paymentProofs as $index => $proof)
                                        @php
                                            // Handle different proof formats
                                            $proofPath = is_array($proof) ? ($proof['path'] ?? $proof) : $proof;
                                            $proofAmount = is_array($proof) ? ($proof['amount'] ?? 0) : ($booking->dp_amount ?? 0);
                                            $proofDate = is_array($proof) && isset($proof['created_at']) ? $proof['created_at'] : null;
                                        @endphp
                                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <label class="form-label fw-bold mb-1">
                                                        Bukti Pembayaran #{{ $loop->iteration }}
                                                    </label>
                                                    <p class="mb-1">
                                                        <strong class="text-primary">Jumlah: Rp {{ number_format($proofAmount, 0, ',', '.') }}</strong>
                                                    </p>
                                                    @if($proofDate)
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($proofDate)->format('d M Y H:i') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <a href="{{ storage_url($proofPath) }}" target="_blank" 
                                                   class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-download me-1"></i>Lihat
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="mt-3 pt-3 border-top">
                                        <label class="form-label fw-bold">Total DP yang Dibayar</label>
                                        <p class="mb-0">
                                            <strong class="text-success fs-5">Rp {{ number_format($booking->dp_amount ?? 0, 0, ',', '.') }}</strong>
                                        </p>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Belum ada bukti pembayaran DP yang diupload.
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

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}
</style>
@endsection


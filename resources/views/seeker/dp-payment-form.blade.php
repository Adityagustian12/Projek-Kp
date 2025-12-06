@extends('layouts.app')

@section('title', 'Lunasi DP - Dashboard Pencari Kosan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="p-3">
                <h6 class="text-muted text-uppercase mb-3">Menu Pencari Kosan</h6>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('seeker.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('bookings.my') }}">
                        <i class="fas fa-calendar-check me-2"></i>Booking Saya
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
                            <i class="fas fa-money-bill-wave me-2"></i>Lunasi DP Booking
                        </h2>
                    </div>
                    <div>
                        <a href="{{ route('seeker.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <!-- Payment Form -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>Form Pembayaran DP
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('seeker.bookings.dp-payment.store', $booking) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <!-- DP Information Alert -->
                                    <div class="alert alert-info mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-info-circle me-2 mt-1"></i>
                                            <div>
                                                <strong>Informasi Pembayaran DP:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li><strong>Harga Kosan per Bulan:</strong> Rp {{ number_format($roomPrice, 0, ',', '.') }}</li>
                                                    @if($paidDp > 0)
                                                        <li><strong>DP yang Sudah Dibayar:</strong> Rp {{ number_format($paidDp, 0, ',', '.') }}</li>
                                                        <li><strong>Sisa yang Harus Dibayar:</strong> <span class="text-danger fw-bold">Rp {{ number_format($remainingDp, 0, ',', '.') }}</span></li>
                                                    @else
                                                        <li><strong>DP yang Sudah Dibayar:</strong> Belum ada</li>
                                                        <li><strong>Sisa yang Harus Dibayar:</strong> <span class="text-danger fw-bold">Rp {{ number_format($roomPrice, 0, ',', '.') }}</span></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Amount -->
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Jumlah DP <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                   id="amount" name="amount" value="{{ old('amount', $suggestedAmount) }}" 
                                                   min="{{ $remainingDp > 0 && $remainingDp < 200000 ? $remainingDp : 200000 }}" 
                                                   max="{{ $remainingDp > 0 ? $remainingDp : '' }}"
                                                   step="1000" required>
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            @if($remainingDp > 0)
                                                <strong>Sisa yang harus dibayar: Rp {{ number_format($remainingDp, 0, ',', '.') }}</strong> (Nominal otomatis terisi)
                                                @if($remainingDp < 200000)
                                                    <br><small class="text-muted">Minimum: Rp {{ number_format($remainingDp, 0, ',', '.') }} (sisa yang harus dibayar)</small>
                                                @else
                                                    <br><small class="text-muted">Minimum: Rp 200.000</small>
                                                @endif
                                            @else
                                                Minimum DP: Rp 200.000
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="mb-3">
                                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_method_bank" value="bank_transfer" {{ old('payment_method', 'bank_transfer') === 'bank_transfer' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="payment_method_bank">
                                                <i class="fas fa-university me-1"></i>Transfer Bank
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_method_dana" value="dana" {{ old('payment_method') === 'dana' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="payment_method_dana">
                                                <i class="fas fa-mobile-alt me-1"></i>DANA
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_method_gopay" value="gopay" {{ old('payment_method') === 'gopay' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="payment_method_gopay">
                                                <i class="fas fa-mobile-alt me-1"></i>GoPay
                                            </label>
                                        </div>
                                        @error('payment_method')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Payment Instructions -->
                                    <div class="alert alert-secondary mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-university me-3 mt-1"></i>
                                            <div class="w-100">
                                                <div class="fw-semibold mb-2">No. Rekening <span class="text-muted">({{ config('app.bank.name') }})</span></div>
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                    <span class="fs-5 fw-bold">{{ config('app.bank.account') }}</span>
                                                    <span class="badge bg-light text-dark">{{ config('app.bank.holder') }}</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyBankAccount()">
                                                        <i class="fas fa-copy me-1"></i>Salin
                                                    </button>
                                                </div>
                                                @if(config('app.bank.note'))
                                                    <div class="small text-muted">{{ config('app.bank.note') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-light border mb-3">
                                        <div class="fw-semibold mb-2"><i class="fas fa-mobile-alt me-2"></i>E-Wallet</div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-info text-dark">DANA</span>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ config('app.ewallets.dana.number') }}</span>
                                                        <small class="text-muted">{{ config('app.ewallets.dana.holder') }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyDana()">
                                                    <i class="fas fa-copy me-1"></i>Salin
                                                </button>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-success">GoPay</span>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ config('app.ewallets.gopay.number') }}</span>
                                                        <small class="text-muted">{{ config('app.ewallets.gopay.holder') }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyGopay()">
                                                    <i class="fas fa-copy me-1"></i>Salin
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Payment Proof -->
                                    <div class="mb-3">
                                        <label for="payment_proof" class="form-label">Bukti Pembayaran DP <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" 
                                               id="payment_proof" name="payment_proof" accept="image/*,.pdf" required>
                                        <div class="form-text">
                                            Upload bukti pembayaran DP (format: JPG, PNG, PDF, maksimal 2MB)
                                        </div>
                                        @error('payment_proof')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Payment Proof Preview -->
                                    <div id="proofPreview" class="mb-3" style="display: none;">
                                        <label class="form-label">Preview Bukti:</label>
                                        <div id="previewContainer" class="text-center">
                                            <img id="previewImage" src="" class="img-fluid rounded" style="max-height: 300px;" alt="Preview Bukti Pembayaran">
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Catatan</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="3" 
                                                  placeholder="Tambahkan catatan terkait pembayaran DP (opsional)...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Bukti DP
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Booking Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>Detail Booking
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>Kamar:</strong> {{ $booking->room->room_number }}
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Harga per Bulan:</strong>
                                        <h5 class="text-success mb-0">Rp {{ number_format($roomPrice, 0, ',', '.') }}</h5>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Check-in:</strong> {{ $booking->check_in_date->format('d M Y') }}
                                    </div>
                                    @if($booking->check_out_date)
                                    <div class="col-12 mb-2">
                                        <strong>Check-out:</strong> {{ $booking->check_out_date->format('d M Y') }}
                                    </div>
                                    @endif
                                    <div class="col-12 mb-2">
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }}">
                                            {{ [
                                                'pending' => 'Menunggu',
                                                'confirmed' => 'Dikonfirmasi',
                                                'rejected' => 'Ditolak',
                                            ][$booking->status] ?? ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <hr class="my-2">
                                    </div>
                                    @if($paidDp > 0)
                                    <div class="col-12 mb-2">
                                        <strong>DP yang Sudah Dibayar:</strong>
                                        <h6 class="text-primary mb-0">Rp {{ number_format($paidDp, 0, ',', '.') }}</h6>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <strong>Sisa yang Harus Dibayar:</strong>
                                        <h5 class="text-danger mb-0">Rp {{ number_format($remainingDp, 0, ',', '.') }}</h5>
                                    </div>
                                    @else
                                    <div class="col-12 mb-3">
                                        <strong>Total DP yang Harus Dibayar:</strong>
                                        <h5 class="text-danger mb-0">Rp {{ number_format($roomPrice, 0, ',', '.') }}</h5>
                                        <small class="text-muted">Belum ada DP yang dibayar</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-phone me-2"></i>Butuh Bantuan?
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong>Hubungi Admin:</strong><br>
                                    📞 081319623603
                                </p>
                                <p class="mb-0">
                                    <strong>Jam Operasional:</strong><br>
                                    Senin - Jumat: 08:00 - 17:00<br>
                                    Sabtu: 08:00 - 12:00
                                </p>
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

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}
</style>

<script>
// Copy functions that also select payment method
function copyBankAccount() {
    const accountNumber = '{{ config('app.bank.account') }}';
    navigator.clipboard.writeText(accountNumber).then(function() {
        document.getElementById('payment_method_bank').checked = true;
        alert('Nomor rekening berhasil disalin! Metode pembayaran: Transfer Bank');
    });
}

function copyDana() {
    const danaNumber = '{{ config('app.ewallets.dana.number') }}';
    navigator.clipboard.writeText(danaNumber).then(function() {
        document.getElementById('payment_method_dana').checked = true;
        alert('Nomor DANA berhasil disalin! Metode pembayaran: DANA');
    });
}

function copyGopay() {
    const gopayNumber = '{{ config('app.ewallets.gopay.number') }}';
    navigator.clipboard.writeText(gopayNumber).then(function() {
        document.getElementById('payment_method_gopay').checked = true;
        alert('Nomor GoPay berhasil disalin! Metode pembayaran: GoPay');
    });
}

// Payment proof preview functionality
document.getElementById('payment_proof').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewContainer = document.getElementById('previewContainer');
    const proofPreview = document.getElementById('proofPreview');
    const previewImage = document.getElementById('previewImage');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            proofPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else if (file && file.type === 'application/pdf') {
        previewContainer.innerHTML = '<div class="alert alert-info"><i class="fas fa-file-pdf fa-2x"></i><br>File PDF terpilih</div>';
        proofPreview.style.display = 'block';
    } else {
        proofPreview.style.display = 'none';
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('amount').value);
    const paymentProof = document.getElementById('payment_proof').files[0];
    
    if (!paymentProof) {
        e.preventDefault();
        alert('Mohon upload bukti pembayaran DP!');
        return false;
    }
    
    if (amount < 200000) {
        e.preventDefault();
        alert('Jumlah DP minimal Rp 200.000!');
        return false;
    }
});
</script>
@endsection


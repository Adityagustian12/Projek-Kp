@extends('layouts.app')

@section('title', 'Form Booking - Kos-Kosan H.Kastim')

@section('content')
<div class="container">
    

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Form Booking Kamar
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Room Info -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">{{ $room->room_number }}</h5>
                        <p class="mb-0">
                            <strong>Harga:</strong> Rp {{ number_format($room->price, 0, ',', '.') }}/bulan
                            <br>
                            <strong>Kapasitas:</strong> {{ $room->capacity }} Orang
                        </p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('seeker.bookings.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="check_in_date" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control @error('check_in_date') is-invalid @enderror" 
                                       id="check_in_date" name="check_in_date" 
                                       value="{{ old('check_in_date') }}" required>
                            </div>
                        </div>

                        <!-- Data Diri Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Data Diri
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" 
                                               placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" 
                                               placeholder="Masukkan email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" 
                                               placeholder="Masukkan nomor telepon" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                               id="address" name="address" value="{{ old('address', auth()->user()->address ?? '') }}" 
                                               placeholder="Masukkan alamat lengkap" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label">Upload KTP</label>
                            <input type="file" class="form-control @error('documents.*') is-invalid @enderror" 
                                   id="documents" name="documents[]" multiple 
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">
                                Upload KTP (JPG, PNG, PDF)
                            </div>
                        </div>

                        <!-- DP Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-wallet me-2"></i>Pembayaran DP
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-secondary mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-university me-3 mt-1"></i>
                                        <div class="w-100">
                                            <div class="fw-semibold mb-2">No. Rekening <span class="text-muted">({{ config('app.bank.name') }})</span></div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <span class="fs-5 fw-bold">{{ config('app.bank.account') }}</span>
                                                <span class="badge bg-light text-dark">{{ config('app.bank.holder') }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ config('app.bank.account') }}')">
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
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ config('app.ewallets.dana.number') }}')">
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
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ config('app.ewallets.gopay.number') }}')">
                                                <i class="fas fa-copy me-1"></i>Salin
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Nominal DP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" min="200000" step="1000" 
                                               value="{{ old('amount', 200000) }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Minimal Rp 200.000</div>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">Bukti Pembayaran DP <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" 
                                           id="payment_proof" name="payment_proof" accept="image/*,.pdf" required>
                                    @error('payment_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG, PDF. Maks 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan Tambahan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Catatan tambahan untuk admin...">{{ old('notes') }}</textarea>
                        </div>

                        

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('public.room.detail', $room) }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Submit Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set minimum date to tomorrow
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const checkInDate = document.getElementById('check_in_date');
        
        checkInDate.min = tomorrow.toISOString().split('T')[0];
    });
</script>
@endsection

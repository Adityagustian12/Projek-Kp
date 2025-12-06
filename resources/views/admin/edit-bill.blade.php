@extends('layouts.app')

@section('title', 'Edit Tagihan - Admin Dashboard')

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
                            <i class="fas fa-edit me-2"></i>Edit Tagihan #{{ $bill->id }}
                        </h2>
                        <div class="text-muted mt-1">
                            {{ $bill->user->name }} - Kamar {{ $bill->room->room_number }}
                        </div>
                    </div>
                    <a href="{{ route('admin.bills') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Edit Bill Form -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>Form Edit Tagihan
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.bills.update', $bill) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Readonly Info -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Penghuni</label>
                                            <input type="text" class="form-control" value="{{ $bill->user->name }} - {{ $bill->user->email }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kamar</label>
                                            <input type="text" class="form-control" value="{{ $bill->room->room_number }}" readonly>
                                        </div>
                                    </div>

                                    <!-- Due date and period -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="due_date" class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                                       value="{{ old('due_date', $bill->due_date->format('Y-m-d')) }}" required>
                                            </div>
                                            @error('due_date')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Periode otomatis mengikuti tanggal jatuh tempo</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Periode Tagihan</label>
                                            <input type="text" class="form-control" id="period_display" value="{{ \Carbon\Carbon::create($bill->year, $bill->month, 1)->translatedFormat('F Y') }}" readonly>
                                        </div>
                                    </div>

                                    <!-- Amount and status -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="amount" class="form-label">Sewa Kamar <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control no-spinner" id="amount" name="amount" 
                                                       value="{{ old('amount', (int)$bill->amount) }}" required min="0" step="1000">
                                            </div>
                                            @error('amount')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" {{ old('status', $bill->status) === 'pending' ? 'selected' : '' }}>Belum Dibayar</option>
                                                <option value="overdue" {{ old('status', $bill->status) === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                                                <option value="paid" {{ old('status', $bill->status) === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                            </select>
                                            @error('status')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Total Amount Display -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="total-card p-3 rounded">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fas fa-calculator fa-lg text-primary"></i>
                                                        <span class="fw-semibold">Total Tagihan</span>
                                                    </div>
                                                    <h4 class="mb-0" id="total_amount_display">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                        <a href="{{ route('admin.bills') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                    </div>
                                </form>
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

.nav-link.active {
    background-color: #0d6efd;
    color: white;
}

.total-card {
    background: #e9f3ff;
    border: 1px solid #bcdcff;
}

/* remove up/down arrows on number input */
.no-spinner::-webkit-outer-spin-button,
.no-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.no-spinner {
    -moz-appearance: textfield;
}
</style>

<script>
function calculateTotal() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    document.getElementById('total_amount_display').textContent = 'Rp ' + amount.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
    const dueDateInput = document.getElementById('due_date');
    const periodDisplay = document.getElementById('period_display');
    function updatePeriod() {
        if (dueDateInput.value) {
            const d = new Date(dueDateInput.value);
            const monthName = d.toLocaleString('id-ID', { month: 'long' });
            periodDisplay.value = `${monthName} ${d.getFullYear()}`;
        } else {
            periodDisplay.value = '';
        }
    }
    updatePeriod();
    dueDateInput.addEventListener('change', updatePeriod);
    document.getElementById('amount').addEventListener('input', calculateTotal);
});

// Show success/error messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endsection



@extends('layouts.app')

@section('title', 'Buat Tagihan Baru - Admin Dashboard')

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
                            <i class="fas fa-plus me-2"></i>Buat Tagihan Baru
                        </h2>
                        
                    </div>
                    <a href="{{ route('admin.bills') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8" id="form_col">
                        <!-- Create Bill Form -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>Form Tagihan Baru
                                </h5>
                                <small class="text-muted">Isi data singkat di bawah ini</small>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.bills.store') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Basic Information -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label">Penghuni <span class="text-danger">*</span></label>
                                            <select class="form-select" id="user_id" name="user_id" required>
                                                <option value="">Pilih Penghuni</option>
                                                @foreach($tenants as $tenant)
                                                    <option value="{{ $tenant->id }}" {{ old('user_id') == $tenant->id ? 'selected' : '' }}>
                                                        {{ $tenant->name }} - {{ $tenant->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kamar</label>
                                            <input type="text" class="form-control readonly-field" id="room_display" value="" placeholder="Otomatis mengikuti kamar yang ditempati" readonly>
                                            <input type="hidden" id="room_id" name="room_id" value="">
                                            @error('room_id')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Due date only (month & year derived) -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="due_date" class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                                       value="{{ old('due_date') }}" required>
                                            </div>
                                            @error('due_date')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Periode otomatis diisi berdasarkan tanggal ini</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Periode Tagihan</label>
                                            <input type="text" class="form-control readonly-field" id="period_display" placeholder="Bulan & Tahun otomatis dari jatuh tempo" readonly>
                                        </div>
                                    </div>

                                    <!-- Amount Information -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="amount" class="form-label">Sewa Kamar <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control no-spinner" id="amount" name="amount" 
                                                       value="{{ old('amount') }}" required min="0" step="1000">
                                            </div>
                                            @error('amount')
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
                                                    <h4 class="mb-0" id="total_amount_display">Rp 0</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Buat Tagihan
                                        </button>
                                        <a href="{{ route('admin.bills') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 d-none" id="tenant_sidebar">
                        <!-- Tenant Info Card -->
                        <div class="card mt-3" id="tenant_info_card" style="display: none;">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Info Penghuni
                                </h5>
                            </div>
                            <div class="card-body" id="tenant_info_content">
                                <!-- Will be populated by JavaScript -->
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

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.readonly-field {
    background-color: #f8f9fa;
    cursor: not-allowed;
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
// Tenant data untuk JavaScript
const tenants = @json($tenants);

// Calculate total amount
function calculateTotal() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    document.getElementById('total_amount_display').textContent = 'Rp ' + amount.toLocaleString('id-ID');
}

// Update room options based on selected tenant
document.getElementById('user_id').addEventListener('change', function() {
    const userId = this.value;
    const roomHidden = document.getElementById('room_id');
    const roomDisplay = document.getElementById('room_display');
    const sidebar = document.getElementById('tenant_sidebar');
    const formCol = document.getElementById('form_col');
    const tenantInfoCard = document.getElementById('tenant_info_card');
    const tenantInfoContent = document.getElementById('tenant_info_content');
    
    // Clear room info
    roomHidden.value = '';
    roomDisplay.value = '';
    
    if (userId) {
        const tenant = tenants.find(t => t.id == userId);
        
        if (tenant && tenant.bookings && tenant.bookings.length > 0) {
            // Get first occupied room (assume satu kamar aktif)
            const occupied = tenant.bookings.find(b => b.status === 'occupied' && b.room);
            if (occupied) {
                roomHidden.value = occupied.room.id;
                roomDisplay.value = `${occupied.room.room_number} - Rp ${parseInt(occupied.room.price).toLocaleString('id-ID')}/bulan`;
            } else {
                roomDisplay.value = 'Tidak ada kamar yang sedang ditempati';
            }
            
            // Show tenant info
            tenantInfoContent.innerHTML = `
                <div class="row">
                    <div class="col-12 mb-2">
                        <strong>Nama:</strong> ${tenant.name}
                    </div>
                    <div class="col-12 mb-2">
                        <strong>Email:</strong> ${tenant.email}
                    </div>
                    <div class="col-12 mb-2">
                        <strong>Telepon:</strong> ${tenant.phone || 'Tidak ada'}
                    </div>
                    <div class="col-12">
                        <strong>Alamat:</strong> ${tenant.address || 'Tidak ada'}
                    </div>
                </div>
            `;
            tenantInfoCard.style.display = 'block';
            sidebar.classList.remove('d-none');
            formCol.className = 'col-lg-8';
        } else {
            roomDisplay.value = 'Penghuni belum memiliki booking aktif';
            sidebar.classList.add('d-none');
            formCol.className = 'col-lg-12';
            // Show tenant info
            if (tenant) {
                tenantInfoContent.innerHTML = `
                    <div class="row">
                        <div class="col-12 mb-2">
                            <strong>Nama:</strong> ${tenant.name}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>Email:</strong> ${tenant.email}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>Telepon:</strong> ${tenant.phone || 'Tidak ada'}
                        </div>
                        <div class="col-12">
                            <strong>Alamat:</strong> ${tenant.address || 'Tidak ada'}
                        </div>
                    </div>
                `;
                tenantInfoCard.style.display = 'block';
                sidebar.classList.remove('d-none');
                formCol.className = 'col-lg-8';
            }
        }
    } else {
        tenantInfoCard.style.display = 'none';
        sidebar.classList.add('d-none');
        formCol.className = 'col-lg-12';
    }
});

// Add event listeners for amount inputs
['amount'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateTotal);
});

// Set default due date to next month
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
    const dueDateInput = document.getElementById('due_date');
    const periodDisplay = document.getElementById('period_display');
    const sidebar = document.getElementById('tenant_sidebar');
    const formCol = document.getElementById('form_col');
    
    // Only set if not already set by old() value
    if (!dueDateInput.value) {
        dueDateInput.value = nextMonth.toISOString().split('T')[0];
    }
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
    // default layout: hide sidebar and expand form
    sidebar.classList.add('d-none');
    formCol.className = 'col-lg-12';
    calculateTotal();
});

// Show success/error messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif

// Debug form submission
document.querySelector('form').addEventListener('submit', function(e) {
    console.log('Form submitted');
    console.log('User ID:', document.getElementById('user_id').value);
    console.log('Room ID:', document.getElementById('room_id').value);
    console.log('Amount:', document.getElementById('amount').value);
    console.log('Due Date:', document.getElementById('due_date').value);
    
    // Validate form before submission
    const userId = document.getElementById('user_id').value;
    const roomId = document.getElementById('room_id').value;
    const amount = document.getElementById('amount').value;
    const dueDate = document.getElementById('due_date').value;
    
    if (!userId || !roomId || !amount || !dueDate) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi.');
        return false;
    }
    
    
    // Check if amount is valid
    if (parseFloat(amount) <= 0) {
        e.preventDefault();
        alert('Jumlah sewa kamar harus lebih dari 0.');
        return false;
    }
});
</script>
@endsection

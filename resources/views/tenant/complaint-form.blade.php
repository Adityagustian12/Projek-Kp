@extends('layouts.app')

@section('title', 'Ajukan Keluhan - Dashboard Penghuni')

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
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Ajukan Keluhan Baru
                        </h2>
                    </div>
                    <div>
                        <a href="{{ route('tenant.complaints') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>

                <!-- Complaint Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>Form Keluhan
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('tenant.complaints.store') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi Keluhan <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="8" 
                                                  placeholder="Jelaskan keluhan Anda secara detail..." required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Keluhan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Tips Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>Tips Mengajukan Keluhan
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Deskripsi Detail:</strong> Jelaskan masalah secara lengkap dengan waktu dan lokasi
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Informasi Lengkap:</strong> Sertakan detail yang membantu admin memahami masalah
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Bahasa Sopan:</strong> Gunakan bahasa yang sopan dan konstruktif
                                    </li>
                                </ul>
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

</style>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const description = document.getElementById('description').value.trim();
    
    if (!description) {
        e.preventDefault();
        alert('Mohon isi deskripsi keluhan!');
        return false;
    }
    
    if (description.length < 10) {
        e.preventDefault();
        alert('Deskripsi keluhan minimal 10 karakter!');
        return false;
    }
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

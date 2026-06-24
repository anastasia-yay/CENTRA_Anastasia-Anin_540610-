@extends('layouts.app') 

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Tombol Kembali --}}
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">Import Data Kejadian Bencana</h4>
                <a href="{{ url('/kejadian') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- Alert Jika Sukses --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Alert Jika Ada Error Validasi Baris Excel --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong class="d-block mb-2">Gagal Mengimport! Periksa kembali file Anda:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Form Upload Excel --}}
                    {{-- PENTING: Harus menggunakan enctype="multipart/form-data" agar file bisa terkirim --}}
                    <form action="{{ url('/kejadian/import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">Pilih File Excel / CSV</label>
                            <input type="file"
                                   name="file"
                                   id="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".xlsx, .xls, .csv"
                                   required>

                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text text-muted mt-2">
                                <small class="d-block text-warning fw-medium">⚠️ Aturan Format Heading/Header Excel (Baris Pertama):</small>
                                <code class="bg-light p-1 rounded d-inline-block mt-1 border text-dark">
                                    disaster_type_id | region_id | jumlah_kejadian | jumlah_korban | tanggal_kejadian | status | keterangan
                                </code>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success p-2">
                                <i class="bi bi-file-earmark-excel"></i> Proses Import Data Excel
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

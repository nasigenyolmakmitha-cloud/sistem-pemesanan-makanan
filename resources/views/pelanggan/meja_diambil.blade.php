@extends('layouts.app')
@section('content')
<div class="row justify-content-center mt-3 mt-md-5">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="card p-4 p-md-5 border-0 shadow-lg rounded-4">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3 shadow-sm" style="width: 100px; height: 100px;">
                    <i class="fad fa-clock text-warning display-4"></i>
                </div>
            </div>
            <h2 class="fw-extrabold text-dark mb-3">Meja Sedang Aktif</h2>
            <p class="text-dark fs-5 mb-4 opacity-75">
                Mohon maaf, <strong>{{ $meja->nomor_meja }}</strong> saat ini sedang digunakan oleh pelanggan lain untuk memesan.
            </p>
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 text-start small">
                <i class="fas fa-exclamation-triangle me-2 text-orange"></i> Satu kode QR hanya dapat diakses oleh satu perangkat (HP) dalam satu waktu untuk menjaga keamanan data pesanan Anda.
            </div>
    </div>
</div>
@endsection

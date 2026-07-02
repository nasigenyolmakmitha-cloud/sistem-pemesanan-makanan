@extends('layouts.app')
@section('content')
<div class="row justify-content-center mt-3 mt-md-5">
    <div class="col-md-8 col-lg-6">
        <div class="card bg-orange p-4 p-md-5 text-center mb-4 position-relative overflow-hidden shadow-lg border-0" style="background: #F4511E !important;">
            <i class="fad fa-utensils position-absolute" style="opacity: 20%; font-size: 15rem; right: -50px; bottom: -50px; color: white;"></i>
            <div class="position-relative z-1 text-white">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3 shadow" style="width: 75px; height: 75px;">
                    <i class="fad fa-store text-orange fs-2"></i>
                </div>
                <h1 class="fw-black mb-2 display-5 text-white">Hai!</h1>
                <p class="mb-0 fs-5 text-white opacity-75 font-weight-light">Selamat datang di<br><span class="fw-black text-white" style="font-size: 1.4rem;">Nasi Be Genyol Mak Mitha</span></p>
            </div>
        </div>
        
        <div class="card p-4 p-md-5 border-0 shadow-sm mt-n4 position-relative z-3">
            <div class="text-center mb-4">
                <span class="badge badge-orange fs-6 px-4 py-2 rounded-pill shadow-sm fw-semibold">
                    <i class="fas fa-map-marker-alt me-2"></i> Posisi: {{ $meja->nomor_meja }}
                </span>
            </div>
            <form action="/pesan/{{ $meja->qr_token }}/nama" method="POST">
                @csrf
                <div class="mb-4 text-start">
                    <label class="form-label fw-extrabold text-dark fs-5 mb-3 px-1"><i class="fal fa-pencil-alt me-2 text-orange"></i> Nama Panggilan Anda</label>
                    <div class="input-group input-group-lg shadow rounded-4 overflow-hidden border-3" style="border-color: #FFAB91 !important; transition: all 0.3s ease;">
                        <span class="input-group-text bg-white border-0 text-orange ps-4 pe-2"><i class="fad fa-user-circle fs-3"></i></span>
                        <input type="text" name="nama" class="form-control border-0 bg-white shadow-none fs-4 py-3 text-dark fw-bold" placeholder="Ketik nama di sini..." required autocomplete="off" style="letter-spacing: 0.5px;">
                    </div>
                    <small class="text-muted mt-2 d-block px-1 fw-medium">Contoh: Bli Gung, Mbak Sari, dll.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-4 shadow-lg d-flex align-items-center justify-content-center fw-black fs-4 py-3 mt-4" style="background: linear-gradient(135deg, #FF7043 0%, #F4511E 100%); border: none;">
                    MULAI MEMESAN <i class="fas fa-chevron-right ms-3 fs-5"></i>
                </button>
            </form>
            <div class="text-center mt-4">
                <small class="text-muted"><i class="fal fa-shield-check text-success"></i> Pesanan Aman & Responsif</small>
            </div>
        </div>
    </div>
</div>
@endsection
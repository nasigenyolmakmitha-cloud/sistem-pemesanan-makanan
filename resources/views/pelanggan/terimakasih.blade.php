@extends('layouts.app')
@section('content')
<div class="row justify-content-center align-items-center min-vh-75">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="card border-0 shadow-lg rounded-5 overflow-hidden p-5">
            <div class="mb-4">
                <div style="width: 200px; height: 200px; margin: 0 auto;">
                    <dotlottie-player src="{{ asset('lottie/Order Received imagery.lottie') }}" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></dotlottie-player>
                </div>
            </div>
            
            <h1 class="fw-bold text-dark mb-3">Terima Kasih!</h1>
            <p class="text-muted fs-5 mb-4">Senang melayani Anda. <br>Pembayaran telah dikonfirmasi dan sesi meja telah selesai.</p>
            
            <div class="divider mb-4"></div>
            
            <h5 class="fw-semibold text-orange mb-4">Nasi Be Genyol Mak Mitha</h5>
            
            <a href="/pesan/{{ $meja->qr_token }}" class="btn btn-outline-orange w-100 py-3 rounded-pill fw-bold">
                <i class="fad fa-redo me-2"></i> Pesan Lagi
            </a>
            
            <p class="small text-muted mt-4">Sampai jumpa kembali!</p>
        </div>
    </div>
</div>

<style>
    .min-vh-75 { min-height: 75vh; }
    .text-orange { color: #f26522; }
    .btn-outline-orange { 
        color: #f26522; 
        border-color: #f26522; 
    }
    .btn-outline-orange:hover {
        background-color: #f26522;
        color: white;
    }
    .divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #eee, transparent);
    }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
@endsection

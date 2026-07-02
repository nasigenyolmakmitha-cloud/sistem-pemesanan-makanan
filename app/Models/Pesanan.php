<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = ['pemesan_id', 'sesi_id', 'status', 'catatan'];

    public function pemesan()
    {
        return $this->belongsTo(Pemesan::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPemesanan::class, 'sesi_id');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function totalHarga()
    {
        return $this->detailPesanan->sum('subtotal');
    }
}
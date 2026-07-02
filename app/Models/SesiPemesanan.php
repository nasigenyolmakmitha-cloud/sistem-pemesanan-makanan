<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiPemesanan extends Model
{
    use HasFactory;

    protected $fillable = ['meja_id', 'kode_sesi', 'status', 'dibuka_pada', 'ditutup_pada'];

    protected $casts = [
        'dibuka_pada' => 'datetime',
        'ditutup_pada' => 'datetime',
    ];

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function pemesan()
    {
        return $this->hasMany(Pemesan::class, 'sesi_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'sesi_id');
    }
}
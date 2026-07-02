<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesan extends Model
{
    use HasFactory;

    protected $fillable = ['sesi_id', 'nama'];

    public function sesi()
    {
        return $this->belongsTo(SesiPemesanan::class, 'sesi_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'pemesan_id');
    }
}
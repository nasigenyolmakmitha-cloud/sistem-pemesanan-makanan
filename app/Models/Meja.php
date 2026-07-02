<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    use HasFactory;

    protected $fillable = ['nomor_meja', 'qr_token'];

    public function sesiPemesanan()
    {
        return $this->hasMany(SesiPemesanan::class);
    }

    public function sesiAktif()
    {
        return $this->hasOne(SesiPemesanan::class)->where('status', 'aktif');
    }
}
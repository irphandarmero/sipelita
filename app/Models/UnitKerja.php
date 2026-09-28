<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja';

    protected $fillable = [
        'nama_unit',
        'kode_unit',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'unit_kerja_id');
    }

    public function detailLaporan()
    {
        return $this->hasMany(DetailLaporan::class, 'unit_kerja_id'); 
    }

    public function laporanAsal()
    {
        return $this->hasMany(DetailLaporan::class, 'unit_asal_id');
    }

    public function laporanTujuan()
    {
        return $this->hasMany(DetailLaporan::class, 'unit_tujuan_id');
    }
}

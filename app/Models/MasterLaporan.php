<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLaporan extends Model
{
    use HasFactory;

    protected $table = 'master_laporan';

    protected $fillable = [
        'nama_laporan',
        'deskripsi',
        'status_aktif',
    ];

    public function detailLaporan()
    {
        return $this->hasMany(DetailLaporan::class, 'master_laporan_id');
    }
}

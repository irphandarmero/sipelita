<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLaporan extends Model
{
    use HasFactory;

    protected $table = 'detail_laporan';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'unit_asal_id',
        'unit_tujuan_id',
        'master_laporan_id',
        'tanggal_kejadian',
        'judul_laporan',
        'deskripsi_kejadian',
        'tindakan_awal',
        'prioritas',
        'status_laporan',
        'petugas_penangan_id',
        'tanggal_penyelesaian',
        'lampiran_file',
    ];

    protected $casts = [
        'tanggal_kejadian'     => 'datetime',
        'tanggal_penyelesaian' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unitAsal()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_asal_id');
    }

    public function unitTujuan()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_tujuan_id');
    }

    public function masterLaporan()
    {
        return $this->belongsTo(MasterLaporan::class, 'master_laporan_id');
    }

    public function petugasPenangan()
    {
        return $this->belongsTo(User::class, 'petugas_penangan_id');
    }

    public function riwayatLaporan()
    {
        return $this->hasMany(RiwayatLaporan::class, 'detail_laporan_id')->orderBy('created_at', 'asc');
    }
}

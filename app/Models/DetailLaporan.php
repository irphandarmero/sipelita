<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLaporan extends Model
{
    use HasFactory;

    protected $table = 'detail_laporan';

    protected $fillable = [
        'master_laporan_id',
        'user_id',
        'unit_kerja_id',
        'tanggal_kejadian',
        'judul_laporan',
        'deskripsi_kejadian',
        'tindakan_awal',
        'lampiran_file',
        'status_laporan',
        'catatan_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function masterLaporan()
    {
        return $this->belongsTo(MasterLaporan::class, 'master_laporan_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiLaporan extends Model
{
    use HasFactory;

    protected $table = 'disposisi_laporan';

    protected $fillable = [
        'detail_laporan_id',
        'unit_pengirim_id',
        'unit_penerima_id',
        'petugas_disposisi_id',
        'petugas_tindak_lanjut_id',
        'catatan_disposisi',
        'catatan_tindak_lanjut',
        'status_disposisi',
        'tanggal_disposisi',
        'tanggal_diterima',
        'tanggal_selesai',
        'lampiran_disposisi',        
    ];

    public function detailLaporan()
    {
        return $this->belongsTo(DetailLaporan::class, 'detail_laporan_id');
    }

    public function unitPengirim()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_pengirim_id');
    }

    public function unitPenerima()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_penerima_id');
    }

    public function userPengirim()
    {
        return $this->belongsTo(User::class, 'petugas_disposisi_id');
    }

    public function petugasTindakLanjut()
    {
        return $this->belongsTo(User::class, 'petugas_tindak_lanjut_id');
    }
}
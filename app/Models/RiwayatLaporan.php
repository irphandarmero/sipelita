<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatLaporan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_laporan';

    protected $fillable = [
        'detail_laporan_id',
        'user_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
    ];

    public function detailLaporan()
    {
        return $this->belongsTo(DetailLaporan::class, 'detail_laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UnitKerja;
use App\Models\MasterLaporan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * 
     */
    public function run()
    {
        // seed unit kerja
        $unitSIMRS = UnitKerja::create([
            'nama_unit' => 'SIMRS',
            'kode_unit' => 'UNIT-SIMRS',
        ]);

        $unitIGD = UnitKerja::create([
            'nama_unit' => 'IGD',
            'kode_unit' => 'UNIT-IGD',
        ]);

        $unitRawatInap = UnitKerja::create([
            'nama_unit' => 'RAWAT INAP',
            'kode_unit' => 'UNIT-RANAP',
        ]);

        $unitLaboratorium = UnitKerja::create([
            'nama_unit' => 'LABORATORIUM',
            'kode_unit' => 'UNIT-LAB',
        ]);
    
        $unitInstalasiFarmasi = UnitKerja::create([
            'nama_unit' => 'INSTALASI FARMASI',
            'kode_unit' => 'UNIT-FRM',
        ]);

        $unitGizi = UnitKerja::create([
            'nama_unit' => 'GIZI',
            'kode_unit' => 'UNIT-GIZI',
        ]);

        $unitRekamMedis = UnitKerja::create([
            'nama_unit' => 'REKAM MEDIS',
            'kode_unit' => 'UNIT-RM',
        ]);
    
        $unitRawatJalan = UnitKerja::create([
            'nama_unit' => 'RAWAT JALAN',
            'kode_unit' => 'UNIT-RAJAL',
        ]);

        $unitTPP = UnitKerja::create([
            'nama_unit' => 'TPP (Pendaftaran)',
            'kode_unit' => 'UNIT-TPP',
        ]);

        $unitDireksi = UnitKerja::create([
            'nama_unit' => 'DIREKSI dan Manajemen',
            'kode_unit' => 'UNIT-DIR',
        ]);
// seed user admin
// Password semua akun default: password123
        $admin = User::create([
            'name' => 'Administrator SIMRS',
            'email' => 'admin@sipelita.com',
            'password' => Hash::make('password123'),
            'unit_kerja_id' => $unitSIMRS->id,
            'role' => 'admin',
        ]);

        // seed master laporan
        // master laporan
        $masterSensus = MasterLaporan::create([
            'nama_laporan' => 'Sensus Harian Pasien',
            'jenis_laporan' => 'Rutin',
            'deskripsi' => 'Laporan sensus rutin jumlah pasien rawat inap dan rawat jalan setiap hari.',
            'status_aktif' => true,
        ]);

        $masterSurveiKepuasan = MasterLaporan::create([
            'nama_laporan' => 'Survei Kepuasan Pasien',
            'jenis_laporan' => 'Rutin',
            'deskripsi' => 'Laporan survei kepuasan pasien terhadap pelayanan rumah sakit.',
            'status_aktif' => true,
        ]);

        $masterSumberInformasi = MasterLaporan::create([
            'nama_laporan' => 'Sumber Informasi Pasien',
            'jenis_laporan' => 'Rutin',
            'deskripsi' => 'Laporan mengenai sumber informasi pasien yang datang ke rumah sakit.',
            'status_aktif' => true,
        ]);

    }
}

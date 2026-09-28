<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('detail_laporan', function (Blueprint $table) { 
            $table->id(); 
            $table->string('kode_transaksi', 50)->unique(); // Relasi Pengguna &amp; Unit (Antar-Unit) 
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Pelapor 
            $table->foreignId('unit_asal_id')->constrained('unit_kerja')->cascadeOnDelete(); // Unit asal pelapor 
            $table->foreignId('unit_tujuan_id')->constrained('unit_kerja')->cascadeOnDelete(); // Unit penanggung jawab (IT, IPSRS, dll) 
            $table->foreignId('master_laporan_id')->constrained('master_laporan')->cascadeOnDelete(); // Kategori laporan // Informasi Laporan 
            $table->dateTime('tanggal_kejadian'); 
            $table->string('judul_laporan', 200); 
            $table->text('deskripsi_kejadian'); 
            $table->text('tindakan_awal')->nullable(); 
            $table->string('lampiran_file')->nullable(); // Prioritas &amp; Status 
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi', 'Darurat'])->default('Sedang'); $table->enum('status_laporan', ['Draft', 'Diajukan', 'Diproses', 'Selesai', 'Ditolak'])->default('Diajukan'); // Penanganan &amp; SLA 
            $table->foreignId('petugas_penangan_id')->nullable()->constrained('users')->nullOnDelete(); $table->dateTime('tanggal_penyelesaian')->nullable(); 
            $table->text('catatan_admin')->nullable(); 
            $table->timestamps();
            }
        ); 
    } 
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::dropIfExists('detail_laporan'); 
    } 
};

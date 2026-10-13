<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposisiLaporanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disposisi_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_laporan_id')->constrained('detail_laporan')->cascadeOnDelete();
            $table->foreignId('unit_pengirim_id')->constrained('unit_kerja')->cascadeOnDelete();
            $table->foreignId('unit_penerima_id')->constrained('unit_kerja')->cascadeOnDelete();
            $table->foreignId('petugas_disposisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_disposisi')->nullable();
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->enum('status_disposisi', ['Pending', 'Diproses', 'Selesai', 'Ditolak'])->default('Pending');
            $table->timestamp('tanggal_disposisi')->useCurrent();
            $table->timestamp('tanggal_diterima')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->string('lampiran_disposisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disposisi_laporan');
    }
}

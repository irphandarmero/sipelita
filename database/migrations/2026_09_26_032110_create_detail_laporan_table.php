<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja')->cascadeOnDelete();
            $table->foreignId('master_laporan_id')->constrained('master_laporan')->cascadeOnDelete();
            $table->datetime('tanggal_kejadian');
            $table->string('judul_laporan', 200);
            $table->text('deskripsi_kejadian');
            $table->text('tindakan_awal')->nullable();
            $table->string('lampiran_file')->nullable();
            $table->enum('status_laporan', ['Draft', 'Diajukan', 'Diproses', 'Selesai', 'Ditolak'])->default('Diajukan');
            $table->text('catatan_admin')->nullable();
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
        Schema::dropIfExists('detail_laporan');
    }
};

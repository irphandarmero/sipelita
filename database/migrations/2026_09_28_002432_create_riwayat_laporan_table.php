<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatLaporanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_laporan_id')->constrained('detail_laporan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // User yang melakukan aksi/perubahan
            $table->string('status_sebelumnya', 50)->nullable();
            $table->string('status_baru', 50);
            $table->text('keterangan')->nullable();
            // Catatan penanganan
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
        Schema::dropIfExists('riwayat_laporan');
    }
}

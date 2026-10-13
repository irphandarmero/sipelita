<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetugasTindakLanjutToDisposisiLaporanTable extends Migration
{
    public function up()
    {
        Schema::table('disposisi_laporan', function (Blueprint $table) {
            $table->foreignId('petugas_tindak_lanjut_id')
                ->nullable()
                ->after('petugas_disposisi_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('disposisi_laporan', function (Blueprint $table) {
            $table->dropForeign(['petugas_tindak_lanjut_id']);
            $table->dropColumn('petugas_tindak_lanjut_id');
        });
    }
}

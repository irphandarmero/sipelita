<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('detail_laporan', function (Blueprint $table) {
            $table->string('status_peninjauan')->nullable()->after('status_laporan');
        });

        DB::table('detail_laporan')
            ->where('status_laporan', 'Diajukan')
            ->whereIn('user_id', DB::table('users')->select('id')->where('role', 'petugas'))
            ->update(['status_peninjauan' => 'Menunggu']);
    }

    public function down()
    {
        Schema::table('detail_laporan', function (Blueprint $table) {
            $table->dropColumn('status_peninjauan');
        });
    }
};

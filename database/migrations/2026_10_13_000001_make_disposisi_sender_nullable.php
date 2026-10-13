<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeDisposisiSenderNullable extends Migration
{
    public function up()
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE disposisi_laporan MODIFY petugas_disposisi_id BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE disposisi_laporan ALTER COLUMN petugas_disposisi_id DROP NOT NULL');
        } else {
            throw new RuntimeException("Making the sender nullable is not supported for database driver '{$driver}'.");
        }
    }

    public function down()
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE disposisi_laporan MODIFY petugas_disposisi_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE disposisi_laporan ALTER COLUMN petugas_disposisi_id SET NOT NULL');
        } else {
            throw new RuntimeException("Making the sender non-nullable is not supported for database driver '{$driver}'.");
        }
    }
}

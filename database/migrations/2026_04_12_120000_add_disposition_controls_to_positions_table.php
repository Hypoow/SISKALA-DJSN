<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->boolean('receives_disposition')->nullable()->after('code');
            $table->string('disposition_group_label')->nullable()->after('receives_disposition');
            $table->string('report_target_label')->nullable()->after('disposition_group_label');
        });

        DB::table('positions')
            ->where('code', 'sekretaris_djsn')
            ->update([
                'receives_disposition' => true,
                'disposition_group_label' => 'Pimpinan Sekretariat DJSN',
                'report_target_label' => 'Sekretaris DJSN',
            ]);

        DB::table('positions')
            ->where('code', 'kabag_umum')
            ->update([
                'receives_disposition' => true,
                'disposition_group_label' => 'Pimpinan Sekretariat DJSN',
                'report_target_label' => 'Kepala Bagian Umum',
            ]);

        DB::table('positions')
            ->where('code', 'kasubag_tu_rt')
            ->update([
                'receives_disposition' => true,
                'disposition_group_label' => 'Pimpinan Sekretariat DJSN',
                'report_target_label' => 'Kepala Sub. Bag. TU & Rumah Tangga',
            ]);

        DB::table('positions')
            ->where('code', 'kabag_persidangan')
            ->update([
                'receives_disposition' => true,
                'disposition_group_label' => 'Pimpinan Sekretariat DJSN',
                'report_target_label' => 'Plt/Kabag Persidangan',
            ]);

        DB::table('positions')
            ->where('code', 'kasubag_protokol_humas')
            ->update([
                'receives_disposition' => true,
                'disposition_group_label' => 'Pimpinan Sekretariat DJSN',
                'report_target_label' => 'Kepala Sub. Bag. Protokol & Humas',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn([
                'receives_disposition',
                'disposition_group_label',
                'report_target_label',
            ]);
        });
    }
};

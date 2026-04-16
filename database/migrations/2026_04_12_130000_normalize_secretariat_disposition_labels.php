<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('positions')
            ->where('disposition_group_label', 'Pimpinan Sekretariat DJSN')
            ->update(['disposition_group_label' => 'Sekretariat DJSN']);

        DB::table('users')
            ->where('disposition_group_label', 'Pimpinan Sekretariat DJSN')
            ->update(['disposition_group_label' => 'Sekretariat DJSN']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('positions')
            ->where('disposition_group_label', 'Sekretariat DJSN')
            ->whereIn('code', [
                'sekretaris_djsn',
                'kabag_umum',
                'kasubag_tu_rt',
                'kabag_persidangan',
                'kasubag_protokol_humas',
            ])
            ->update(['disposition_group_label' => 'Pimpinan Sekretariat DJSN']);
    }
};

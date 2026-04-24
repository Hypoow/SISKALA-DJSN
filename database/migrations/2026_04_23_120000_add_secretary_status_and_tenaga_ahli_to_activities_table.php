<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('secretary_disposition_status')
                ->default('disposisi')
                ->after('report_target_override');
            $table->boolean('include_tenaga_ahli')
                ->default(false)
                ->after('secretary_disposition_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'secretary_disposition_status',
                'include_tenaga_ahli',
            ]);
        });
    }
};

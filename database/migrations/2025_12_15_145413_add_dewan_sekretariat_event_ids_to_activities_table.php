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
            $table->string('google_event_id_dewan')->nullable()->after('google_event_id');
            $table->string('google_event_id_sekretariat')->nullable()->after('google_event_id_dewan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['google_event_id_dewan', 'google_event_id_sekretariat']);
        });
    }
};

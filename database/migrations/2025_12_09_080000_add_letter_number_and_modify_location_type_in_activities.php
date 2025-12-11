<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('letter_number')->nullable()->after('type');
        });

        // Modify enum using raw SQL for better compatibility
        DB::statement("ALTER TABLE activities MODIFY COLUMN location_type ENUM('offline', 'online', 'hybrid') DEFAULT 'offline'");
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('letter_number');
        });

        // Revert enum
        DB::statement("ALTER TABLE activities MODIFY COLUMN location_type ENUM('offline', 'online') DEFAULT 'offline'");
    }
};

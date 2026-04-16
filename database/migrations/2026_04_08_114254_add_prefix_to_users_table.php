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
        Schema::table('users', function (Blueprint $table) {
            $table->string('prefix')->default('Bapak')->after('name');
        });
        
        // Update existing female member
        \Illuminate\Support\Facades\DB::table('users')
            ->where('name', 'like', '%Indah Anggoro Putri%')
            ->update(['prefix' => 'Ibu']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('prefix');
        });
    }
};

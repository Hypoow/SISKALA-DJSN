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
        // Change role column to VARCHAR(50) to support 'dewan' and other roles
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to ENUM if needed, but VARCHAR is generally fine. 
        // We'll leave it as VARCHAR or try to revert to previous state if known.
        // For now, let's just keep it as VARCHAR(50) or revert to 'admin','user' ENUM if strictly required.
        // DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user'");
    }
};

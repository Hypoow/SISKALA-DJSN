<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['external', 'internal']);
            $table->string('name');
            $table->dateTime('date_time');
            $table->json('pic')->nullable(); // For Internal
            
            // Statuses
            $table->integer('status')->default(0);
            $table->integer('invitation_status')->default(0);
            
            // Invitation Type
            $table->enum('invitation_type', ['inbound', 'outbound'])->default('inbound'); // inbound=Masuk, outbound=Keluar
            
            // Location
            $table->enum('location_type', ['offline', 'online'])->default('offline');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            
            $table->text('dispo_note')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

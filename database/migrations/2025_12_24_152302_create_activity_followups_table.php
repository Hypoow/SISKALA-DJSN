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
        Schema::create('activity_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->string('topic')->nullable(); // Tema/Pillar
            $table->text('instruction'); // Arahan / Deskripsi Tindak Lanjut
            $table->string('pic')->nullable(); // PIC Kegiatan / Yang Bertugas
            $table->text('progress_notes')->nullable(); // Progres Capaian
            
            // Status: 0=Pending, 1=On Progress, 2=Selesai, 3=Tidak Dilanjut
            $table->integer('status')->default(0);
            
            $table->date('deadline')->nullable();
            $table->text('notes')->nullable(); // Telaah TA
            $table->dateTime('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_followups');
    }
};

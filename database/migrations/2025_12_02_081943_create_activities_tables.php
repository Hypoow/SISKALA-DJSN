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
        Schema::create('external_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('date_time');
            $table->integer('status')->default(0); // 0: On Schedule, 1: Reschedule, 2: Belom ada Dispo, 3: Tidak Dilaksanakan
            $table->integer('invitation_status')->default(0); // 0: Proses Disposisi, 1: Sudah ada Disposisi, 2: Untuk Diketahui Ketua, 3: Terjadwal Hadir
            $table->string('location')->nullable();
            $table->text('dispo_note')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('internal_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('pic')->nullable(); // Komjakum, PME, Sekretariat DJSN
            $table->dateTime('date_time');
            $table->integer('status')->default(0); // 0: On Schedule, 1: Reschedule, 2: Belom ada Dispo, 3: Tidak Dilaksanakan
            $table->integer('invitation_status')->default(0); // 0: Proses Terkirim, 1: Proses TTD, 2: Proses Drafting dan Acc
            $table->string('location')->nullable();
            $table->text('dispo_note')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_activities');
        Schema::dropIfExists('internal_activities');
    }
};

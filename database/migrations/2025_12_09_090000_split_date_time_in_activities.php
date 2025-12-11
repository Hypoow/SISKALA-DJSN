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
            $table->date('start_date')->nullable()->after('name');
            $table->date('end_date')->nullable()->after('start_date');
            $table->time('start_time')->nullable()->after('end_date');
            $table->time('end_time')->nullable()->after('start_time');
        });

        // Migrate existing data
        $activities = DB::table('activities')->get();
        foreach ($activities as $activity) {
            if ($activity->date_time) {
                $dt = new DateTime($activity->date_time);
                DB::table('activities')
                    ->where('id', $activity->id)
                    ->update([
                        'start_date' => $dt->format('Y-m-d'),
                        'end_date' => $dt->format('Y-m-d'), // Default single day
                        'start_time' => $dt->format('H:i:s'),
                        'end_time' => null // Default "Selesai" or explicit end time absent
                    ]);
            }
        }

        // Drop old column
        Schema::table('activities', function (Blueprint $table) {
             $table->dropColumn('date_time');
        });
        
        // Make non-nullable where appropriate
        Schema::table('activities', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->time('start_time')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dateTime('date_time')->nullable();
        });

        // Revert data
        $activities = DB::table('activities')->get();
        foreach ($activities as $activity) {
            if ($activity->start_date && $activity->start_time) {
                $dateTime = $activity->start_date . ' ' . $activity->start_time;
                DB::table('activities')
                    ->where('id', $activity->id)
                    ->update(['date_time' => $dateTime]);
            }
        }

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'start_time', 'end_time']);
        });
    }
};

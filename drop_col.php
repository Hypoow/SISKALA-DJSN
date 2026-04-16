<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
Schema::table('users', function(Blueprint $table) {
    if (Schema::hasColumn('users', 'position_id')) {
        $table->dropColumn('position_id');
    }
});
echo "Column dropped if it existed\n";

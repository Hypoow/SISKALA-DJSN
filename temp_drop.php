<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('users', function (Blueprint $table) {
    if (Schema::hasColumn('users', 'position_id')) {
        $table->dropForeign(['position_id']);
        $table->dropColumn('position_id');
        echo "Dropped constraint and column.\n";
    } else {
        echo "Column does not exist.\n";
    }
});

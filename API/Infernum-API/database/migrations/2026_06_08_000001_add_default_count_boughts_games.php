<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add default value of 0 to count_boughts in games table.
     * This ensures game creation from the admin panel works without
     * requiring the field to be filled in manually.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->integer('count_boughts')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->integer('count_boughts')->default(null)->change();
        });
    }
};

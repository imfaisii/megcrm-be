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
        Schema::table('call_centers', function (Blueprint $table) {
            $table->boolean('is_call_scheduled')->default(false);
            $table->timestamp('call_scheduled_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_centers', function (Blueprint $table) {
            $table->dropColumn('is_call_scheduled');
            $table->dropColumn('call_scheduled_time');
        });
    }
};

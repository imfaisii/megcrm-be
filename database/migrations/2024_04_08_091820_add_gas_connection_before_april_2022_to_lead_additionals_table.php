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
        Schema::table('lead_additionals', function (Blueprint $table) {
            $table->boolean('gas_connection_before_april_2022')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_additionals', function (Blueprint $table) {
            $table->dropColumn('gas_connection_before_april_2022');
        });
    }
};

<?php

use App\Models\Lead;
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
        Schema::create('lead_additional_details', function (Blueprint $table) {
            $table->id();
            $table->string('adress_line_1')->nullable();
            $table->string('adress_line_2')->nullable();
            $table->string('adress_line_3')->nullable();
            $table->string('adress_line_4')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('thorough_fare')->nullable();
            $table->string('building_name')->nullable();
            $table->string('building_number')->nullable();
            $table->string('sub_building_name')->nullable();
            $table->string('sub_building_number')->nullable();
            $table->string('locality')->nullable();
            $table->string('town_or_city')->nullable();
            $table->string('county')->nullable();
            $table->string('country')->nullable();
            $table->string('district')->nullable();
            $table->string('residential')->nullable();
            $table->foreignIdFor(Lead::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_additional_details');
    }
};

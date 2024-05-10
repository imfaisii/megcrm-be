<?php

use App\Models\LeadGenerator;
use App\Models\User;
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
        Schema::create('lead_generator_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LeadGenerator::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_generator_managers');
    }
};

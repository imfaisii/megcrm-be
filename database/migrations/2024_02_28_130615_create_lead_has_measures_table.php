<?php

use App\Models\Lead;
use App\Models\Measure;
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
        Schema::create('lead_has_measures', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Lead::class);
            $table->foreignIdFor(Measure::class);
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_has_measures');
    }
};

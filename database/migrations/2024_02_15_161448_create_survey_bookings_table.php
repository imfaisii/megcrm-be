<?php

use App\Models\Lead;
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
        Schema::create('survey_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('preffered_time')->nullable();
            $table->timestamp('survey_at')->nullable();
            $table->text('comments')->nullable();
            $table->foreignIdFor(User::class, 'surveyor_id')->nullable();
            $table->foreignIdFor(Lead::class)->nullable();
            $table->foreignIdFor(User::class, 'created_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_details');
    }
};

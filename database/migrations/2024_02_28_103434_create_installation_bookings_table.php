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
        Schema::create('installation_bookings', function (Blueprint $table) {
            $table->id();
            $table->timestamp('installation_at')->nullable();
            $table->text('comments')->nullable();
            $table->foreignIdFor(Measure::class);
            $table->foreignIdFor(User::class, 'installer_id')->nullable();
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
        Schema::dropIfExists('installation_bookings');
    }
};

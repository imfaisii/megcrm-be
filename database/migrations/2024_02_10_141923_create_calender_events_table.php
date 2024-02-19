<?php

use App\Models\Calendar;
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
        Schema::create('calender_events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('New Calender Event');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('all_day')->default(false);
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->json('extra_data')->nullable();
            $table->nullableMorphs('eventable');
            $table->foreignIdFor(Calendar::class)->nullable();
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calender_events');
    }
};

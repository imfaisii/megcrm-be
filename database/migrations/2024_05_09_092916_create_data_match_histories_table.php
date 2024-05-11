<?php

use App\Models\Lead;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_match_histories', function (Blueprint $table) {
            $table->id();
            $table->string('datamatch_progress')->nullable();
            $table->dateTime('datamatch_progress_date')->nullable();
            $table->foreignIdFor(Lead::class, 'lead_id')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->text('post_code')->nullable();
            $table->string('urn')->nullable();
            $table->dateTime('data_match_sent_date')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_match_histories');
    }
};

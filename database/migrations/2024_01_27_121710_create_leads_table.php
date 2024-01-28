<?php

use App\Models\BenefitType;
use App\Models\FuelType;
use App\Models\JobType;
use App\Models\LeadGenerator;
use App\Models\LeadSource;
use App\Models\Surveyor;
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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone_no');
            $table->string('dob');
            $table->string('post_code');
            $table->text('address');
            $table->boolean('is_marked_as_job')->default(false);
            $table->foreignIdFor(JobType::class)->nullable();
            $table->foreignIdFor(FuelType::class)->nullable();
            $table->foreignIdFor(Surveyor::class)->nullable();
            $table->foreignIdFor(LeadGenerator::class)->nullable();
            $table->foreignIdFor(LeadSource::class)->nullable();
            $table->foreignIdFor(BenefitType::class)->nullable();
            $table->text('comments')->nullable();
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

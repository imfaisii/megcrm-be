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
        Schema::create('lead_customer_additional_details', function (Blueprint $table) {
            $table->id();
            $table->string('contact_method')->nullable();
            $table->string('priority_type')->nullable();
            $table->string('time_to_contact')->nullable();
            $table->string('time_at_address')->nullable();
            $table->boolean('is_customer_owner')->default(false);
            $table->boolean('is_lead_shared')->default(false);
            $table->boolean('is_datamatch_required')->default(false);
            $table->string('datamatch_progress')->default('Not Sent');
            $table->date('datamatch_progress_date')->nullable();
            $table->foreignIdFor(Lead::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_customer_additional_details');
    }
};

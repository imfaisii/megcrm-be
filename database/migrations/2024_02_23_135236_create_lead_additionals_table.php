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
        Schema::create('lead_additionals', function (Blueprint $table) {
            $table->id();
            $table->boolean('datamatch_confirmed')->default(false);
            $table->boolean('land_registry_confirmed')->default(false);
            $table->boolean('proof_of_address_confirmed')->default(false);
            $table->boolean('epr_report_confirmed')->default(false);
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->foreignIdFor(Lead::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_additionals');
    }
};

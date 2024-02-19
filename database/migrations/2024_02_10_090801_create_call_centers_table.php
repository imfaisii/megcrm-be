<?php

use App\Models\CallCenterStatus;
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
        Schema::create('call_centers', function (Blueprint $table) {
            $table->id();
            $table->text('comments')->nullable();
            $table->foreignIdFor(Lead::class);
            $table->foreignIdFor(CallCenterStatus::class);
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->timestamp('called_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_centers');
    }
};

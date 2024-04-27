<?php

use App\Models\ComplaintMeasures;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->date('propsed_date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('priority', ['low', 'high', 'medium'])->default('low');
            $table->foreignIdFor(ComplaintMeasures::class, 'measure_id');
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->morphs('complaintable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

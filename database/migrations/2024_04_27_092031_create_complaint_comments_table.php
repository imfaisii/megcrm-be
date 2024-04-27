<?php

use App\Models\Complaints;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaint_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->foreignIdFor(Complaints::class,'complaint_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_comments');
    }
};

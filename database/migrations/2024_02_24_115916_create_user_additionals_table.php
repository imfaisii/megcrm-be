<?php

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
        Schema::create('user_additionals', function (Blueprint $table) {
            $table->id();
            $table->string('gender')->nullable();
            $table->string('dob')->nullable();
            $table->string('phone_no')->nullable();
            $table->text('address')->nullable();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(User::class, 'created_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_additionals');
    }
};

<?php

use App\Models\Bank;
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
        Schema::table('user_additionals', function (Blueprint $table) {
            $table->timestamp('visa_expiry')->nullable();
            $table->string('nin')->nullable();
            $table->string('account_number')->nullable();
            $table->foreignIdFor(Bank::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_additionals', function (Blueprint $table) {
            $table->dropColumn([
                'visa_expiry',
                'nin',
                'account_number'
            ]);
        });
    }
};

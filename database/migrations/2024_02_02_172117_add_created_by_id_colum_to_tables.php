<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'job_types',
        'fuel_types',
        'benefit_types',
        'lead_generators',
        'lead_sources',
        'measures',
        'surveyors'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $key => $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignIdFor(User::class, 'created_by_id')->default(1);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $key => $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('created_by_id');
            });
        }
    }
};

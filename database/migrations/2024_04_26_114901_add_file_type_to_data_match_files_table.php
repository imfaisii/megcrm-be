<?php

use App\Enums\AppEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_match_files', function (Blueprint $table) {
            //
            $table->enum('type', AppEnum::allowedFileTypes())->default(AppEnum::FILE_TYPE_DATA_MATCH_DOWNLOAD)->default(AppEnum::FILE_TYPE_DATA_MATCH_UPLOAD);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_match_files', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

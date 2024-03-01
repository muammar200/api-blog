<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_users', function (Blueprint $table) {
            DB::statement('ALTER TABLE detail_users CHANGE COLUMN avatar_url avatar VARCHAR(255)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_users', function (Blueprint $table) {
            // Revert perubahan kolom jika diperlukan
        DB::statement('ALTER TABLE detail_users CHANGE COLUMN avatar avatar_url VARCHAR(255)');
        });
    }
};

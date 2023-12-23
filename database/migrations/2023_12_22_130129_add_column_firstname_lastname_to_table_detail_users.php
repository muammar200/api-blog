<?php

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
        Schema::table('detail_users', function (Blueprint $table) {
            $table->string('firstname')->after('id');
            $table->string('lastname')->after('firstname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_users', function (Blueprint $table) {
            if (Schema::hasColumn('detail_users', 'firstname')) {
                $table->dropColumn('firstname');
            }
            if (Schema::hasColumn('detail_users', 'lastname')) {
                $table->dropColumn('lastname');
            }            
        });
    }
};

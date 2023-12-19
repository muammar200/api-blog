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
        Schema::create('detail_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('phone')->unique()->nullable();
            $table->date('birthdate')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('avatar_url')->nullable();
            $table->text('biography')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->json('social_media_links')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_users');
    }
};

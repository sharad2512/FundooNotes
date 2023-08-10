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
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('index');
            $table->string('title')->nullable();
            $table->string('content')->nullable();
            $table->string('remainder')->nullable();
            $table->boolean('pinned')->default(false);
            $table->boolean('archieved')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
            $table->foreign('Userid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};

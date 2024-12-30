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
        Schema::create('platform_prompt', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('platform_id')->unsigned();
            $table->bigInteger('prompt_id')->unsigned();

            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
            $table->foreign('prompt_id')->references('id')->on('prompts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_prompt');
    }
};

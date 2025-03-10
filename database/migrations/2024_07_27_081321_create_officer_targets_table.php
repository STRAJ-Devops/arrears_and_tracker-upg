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
        Schema::create('officer_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('officer_id');
            $table->foreign('officer_id')->references('staff_id')->on('officers');
            $table->string('target_amount');
            $table->integer('target_numbers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officer_targets');
    }
};

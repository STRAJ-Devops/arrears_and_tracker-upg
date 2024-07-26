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
        Schema::create('sub__counties', function (Blueprint $table) {
            $table->string('subcounty_id')->primary();
            $table->string('subcounty_name');
            $table->string('district_id');
            $table->foreign('district_id')->references('district_id')->on('districts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub__counties');
    }
};

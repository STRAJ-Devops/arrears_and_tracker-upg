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
        Schema::create('officers', function (Blueprint $table) {
            $table->integer('staff_id')->primary();
            $table->string('names', 333);
            $table->smallInteger('user_type');
            $table->string('username', 333);
            $table->integer('region_id')->nullable();
            $table->foreign('region_id')->references('region_id')->on('regions');
            $table->bigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('branch_id')->on('branches');
            $table->string('password');
            $table->string('un_hashed_password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};

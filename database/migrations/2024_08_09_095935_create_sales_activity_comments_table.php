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
        Schema::create('sales_activity_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_activity_id');
            $table->foreign('sales_activity_id')->references('id')->on('monitors');
            $table->integer('officer_id');
            $table->foreign('officer_id')->references('staff_id')->on('officers');
            $table->longText('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_activity_comments');
    }
};

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
        Schema::create('targets', function (Blueprint $table) {

            $table->id();

            $table->bigInteger('branch_id');
            $table->foreign('branch_id')->references('branch_id')->on('branches');

            $table->integer('product_id');
            $table->foreign('product_id')->references('product_id')->on('products');

            $table->bigInteger('target_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};

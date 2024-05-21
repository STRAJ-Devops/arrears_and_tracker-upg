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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->integer('staff_id');

            $table->foreign('staff_id')->references('staff_id')->on('officers');

            $table->integer('product_id');

            $table->foreign('product_id')->references('product_id')->on('products');

            $table->string('gender')->nullable();

            $table->bigInteger('number_of_children');

            $table->string('disbursement_date')->nullable();

            $table->bigInteger('disbursement_amount');
            $table->integer('number_of_group_members')->nullable();

            $table->integer('region_id');
            $table->foreign('region_id')->references('region_id')->on('regions');

            $table->bigInteger('branch_id');
            $table->foreign('branch_id')->references('branch_id')->on('branches');

            $table->string('group_id')->nullable();

            $table->string('number_of_women')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

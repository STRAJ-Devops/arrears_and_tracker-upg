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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id');
            $table->foreign('staff_id')->references('staff_id')->on('officers');

            $table->string('customer_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers');

            $table->text('comment');

            $table->integer('number_of_days_late');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

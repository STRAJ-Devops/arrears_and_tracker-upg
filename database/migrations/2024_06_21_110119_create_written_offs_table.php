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
        Schema::create('written_offs', function (Blueprint $table) {
            $table->id();
            $table->string('officer_name')->nullable();
            $table->string('contract_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('group_id')->nullable();
            $table->string('group_name')->nullable();
            $table->string('customer_phone_number')->nullable();
            $table->string('csa')->nullable();
            $table->string('dda')->nullable();
            $table->string('write_off_date')->nullable();
            $table->string('principal_written_off')->nullable();
            $table->string('interest_written_off')->nullable();
            $table->string('principal_paid')->nullable();
            $table->string('interest_paid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('written_offs');
    }
};

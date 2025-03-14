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
        Schema::create('s_c_v_caches', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->enum('param', ['customerNo', 'officerNo', 'contractNo', 'accountNo', 'phoneNo', 'name']);
            $table->string('key');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_c_v_caches');
    }
};

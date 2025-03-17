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
        Schema::create('write_off_caches', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->enum('param', ['customerNo', 'contractNo', 'officerNo', 'name', 'group', 'phone']);
            $table->string('key');
            $table->unique(['param', 'key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('write_off_caches');
    }
};

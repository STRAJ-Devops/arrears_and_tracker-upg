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
        Schema::create('incentive_settings', function (Blueprint $table) {
            $table->id();
            $table->double('max_par');
            $table->double('percentage_incentive_par');
            $table->string('max_cap_portifolio', 255);
            $table->string('min_cap_portifolio', 255);
            $table->double('percentage_incentive_portifolio');
            $table->integer('max_cap_client');
            $table->integer('min_cap_client');
            $table->double('percentage_incentive_client');
            $table->string('max_incentive', 255);
            $table->string('max_cap_portifolio_individual', 255);
            $table->string('max_cap_portifolio_group', 255);
            $table->integer('min_cap_client_individual');
            $table->integer('min_cap_client_group');
            $table->double('max_par_individual');
            $table->double('max_par_group');
            $table->double('max_par_fast');
            $table->double('max_llr_group');
            $table->double('max_llr_individual');
            $table->double('max_llr_fast');
            $table->integer('max_cap_number_of_groups_fast');
            $table->integer('min_cap_number_of_groups_fast');
            $table->string('max_cap_portifolio_fast', 255);
            $table->string('min_cap_portifolio_fast', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentive_settings');
    }
};

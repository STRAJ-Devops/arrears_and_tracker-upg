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
        Schema::create('arrears', function (Blueprint $table) {
            $table->id();

            $table->integer('staff_id');

            $table->foreign('staff_id')->references('staff_id')->on('officers');

            $table->bigInteger('branch_id');

            $table->foreign('branch_id')->references('branch_id')->on('branches');

            $table->integer('region_id');

            $table->foreign('region_id')->references('region_id')->on('regions');

            $table->integer('product_id');

            $table->foreign('product_id')->references('product_id')->on('products');

            $table->string('district_id');

            $table->foreign('district_id')->references('district_id')->on('districts');

            $table->string('subcounty_id');

            $table->foreign('subcounty_id')->references('subcounty_id')->on('sub__counties');

            $table->integer('village_id');

            $table->foreign('village_id')->references('village_id')->on('villages');

            $table->bigInteger('outsanding_principal');

            //this is interest in arrears
            $table->bigInteger('outstanding_interest');

            $table->bigInteger('principal_arrears');

            //this is add column
            $table->bigInteger('interest_in_arrears');

            $table->bigInteger('number_of_days_late');

            $table->integer('number_of_group_members');

            $table->bigInteger('amount_disbursed');

            $table->string('lending_type');

            $table->string('gender');

            $table->bigInteger('par');

            $table->string('customer_id');

            $table->foreign('customer_id')->references('customer_id')->on('customers');

            $table->bigInteger('next_repayment_principal')->nullable();

            $table->bigInteger('next_repayment_interest')->nullable();

            $table->string('next_repayment_date')->nullable();

            $table->string('group_id')->nullable();

            $table->string('disbursement_date');

            $table->string('number_of_women')->nullable();

            $table->string('draw_down_balance')->nullable();

            $table->string('savings_balance')->nullable();

            //outstanding interest
            $table->string('real_outstanding_interest')->nullable();

            $table->string('group_name')->nullable();

            $table->string('maturity_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrears');
    }
};

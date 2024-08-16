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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->nullable()
                ->references('id')->on('meals')
                ->onDelete('SET NUll')->cascadeOnUpdate();

            $table->foreignId('order_id')->nullable()
                ->references('id')->on('orders')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('meal_description');
            $table->decimal('meal_price');
            $table->decimal('meal_discount');

            $table->unsignedInteger('quantity');

            $table->integer('service')->unsigned()->default(0);
            $table->integer('vat')->unsigned()->default(0);

            $table->decimal('service_amount')->unsigned()->default(0);
            $table->decimal('vat_amount')->unsigned()->default(0);

            $table->decimal('sub_total')->unsigned()->default(0);
            $table->decimal('amount_to_pay')->unsigned();

            $table->string('status');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};

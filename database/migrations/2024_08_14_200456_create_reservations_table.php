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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('from_time');
            $table->dateTime('to_time');
            $table->dateTime('checkin_time')->nullable();
            $table->dateTime('checkout_time')->nullable();
            $table->integer('number_of_guests');

            $table->foreignId('table_id')->nullable()
                ->references('id')->on('tables')
                ->onDelete('SET NUll')->cascadeOnUpdate();

            $table->foreignId('customer_id')->nullable()
                ->references('id')->on('customers')
                ->onDelete('SET NUll')->cascadeOnUpdate();

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
        Schema::dropIfExists('reservations');
    }
};

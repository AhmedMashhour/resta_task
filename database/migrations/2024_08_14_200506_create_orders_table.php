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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()
                ->references('id')->on('tables')
                ->onDelete('SET NUll')->cascadeOnUpdate();

            $table->foreignId('customer_id')->nullable()
                ->references('id')->on('customers')
                ->onDelete('SET NUll')->cascadeOnUpdate();

            $table->foreignId('user_id')->nullable()
                ->references('id')->on('users')
                ->onDelete('SET NUll')->cascadeOnUpdate();

            $table->foreignId('reservation_id')->nullable()
                ->references('id')->on('reservations')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->decimal('total')->unsigned();

            $table->decimal('total_vat')->unsigned()->default(0);
            $table->decimal('total_service')->unsigned()->default(0);
            $table->integer('service')->default(0);
            $table->integer('vat')->default(0);

            $table->decimal('paid')->unsigned()->default(0);
            $table->date('date');

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
        Schema::dropIfExists('orders');
    }
};

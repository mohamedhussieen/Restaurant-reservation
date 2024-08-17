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
                $table->increments('id');
                $table->unsignedInteger('order_id');
                $table->unsignedInteger('meal_id');
                $table->decimal('amount_to_pay', 10, 2);
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
            });
        }

        public function down()
        {
            Schema::dropIfExists('order_details');
        }
};

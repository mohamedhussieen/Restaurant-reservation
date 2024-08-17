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
        $table->increments('id');
        $table->unsignedInteger('table_id');
        $table->unsignedInteger('reservation_id');
        $table->unsignedInteger('customer_id');
        $table->unsignedBigInteger('user_id');
        $table->decimal('total', 10, 2);
        $table->boolean('paid')->default(false);
        $table->date('date');
        $table->timestamps();
        $table->foreign('table_id')->references('id')->on('tables');
        $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
        $table->foreign('customer_id')->references('id')->on('customers');
        $table->foreign('user_id')->references('id')->on('users');
    });
}

public function down()
{
    Schema::dropIfExists('orders');
}
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id')->unique();
            $table->string('user_id');
            $table->decimal('weigth', 5,2);
            $table->integer('package');
            $table->set('status', ['pending', 'waiting', 'washed', 'wet', 'dried', 'rubit', 'packing', 'finish']);
            $table->set('proses', ['pending', 'proses', 'selesai']);
            $table->integer('discount');
            $table->integer('price');
            $table->set('payment', ['cash', 'debt']);
            $table->boolean('payment_status'); // 0 = belum bayar; 1 = telah bayar
            $table->string('staff_in');
            $table->string('staff_out');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

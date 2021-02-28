<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->string('id', 13)->unique();
            $table->decimal('off', 10,2);
            $table->set('type', ['percent', 'cut']);
            $table->boolean('valid_to'); // 0= untuk satu orang; 1= untuk banyak orang selama status aktif (0)
            $table->boolean('status'); // 0= belum digunakan; 1= telah digunakan
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
        Schema::dropIfExists('vouchers');
    }
}

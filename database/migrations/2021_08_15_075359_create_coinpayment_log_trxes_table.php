<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinpaymentLogTrxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coinpayment_log_trxes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('merchant_id');
            $table->string('payment_id');
            $table->string('payment_address');
            $table->string('coin', 10);
            $table->string('fiat', 10);
            $table->string('status_text');
            $table->integer('status')->default(0);
            $table->datetime('payment_created_at');
            $table->datetime('expired');
            $table->datetime('confirmation_at')->nullable();
            $table->double('amount', 20, 8);
            $table->integer('confirms_needed');
            $table->string('qrcode_url');
            $table->string('status_url');
            $table->text('payload');
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
        Schema::dropIfExists('coinpayment_log_trxes');
    }
}

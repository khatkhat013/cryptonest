<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_wallet_transactions')) {
            Schema::create('user_wallet_transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('user_wallet_id')->nullable()->index();
                $table->string('coin', 32)->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('balance_after', 28, 8)->nullable();
                $table->string('type', 32)->default('credit'); // credit|debit
                $table->string('subtype', 64)->nullable(); // e.g., profit, principal_return, hourly_profit
                $table->string('reference_model', 128)->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wallet_transactions');
    }
};

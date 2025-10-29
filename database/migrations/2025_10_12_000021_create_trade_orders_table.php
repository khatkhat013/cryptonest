<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('trade_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('symbol');
            $table->enum('direction', ['up','down']);
            $table->decimal('purchase_quantity', 28, 8);
            $table->decimal('purchase_price', 28, 8);
            $table->decimal('final_price', 28, 8)->nullable();
            $table->integer('price_range_percent')->nullable();
            $table->integer('delivery_seconds');
            $table->decimal('profit_amount', 28, 8)->nullable();
            $table->enum('result', ['pending','win','lose','error'])->default('pending');
            $table->boolean('force_applied')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_orders');
    }
};

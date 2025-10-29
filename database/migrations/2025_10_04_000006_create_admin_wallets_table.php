<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                  ->constrained('admins')
                  ->onDelete('cascade');
            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->onDelete('restrict');
            $table->string('address');
            $table->decimal('coin_amount', 18, 8)->default(500.00000000);
            $table->timestamps();

            // Unique constraint for admin and currency combination
            $table->unique(['admin_id', 'currency_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_wallets');
    }
};
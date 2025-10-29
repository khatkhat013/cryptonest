<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If the conversions table already exists (for example when running migrations
        // against an existing database), skip creating it to avoid errors.
        if (!Schema::hasTable('conversions')) {
            Schema::create('conversions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('from_coin', 32);
                $table->string('to_coin', 32);
                $table->decimal('from_amount', 32, 16);
                $table->decimal('to_amount', 32, 16);
                $table->decimal('rate', 32, 16)->nullable();
                $table->string('status', 32)->default('completed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};

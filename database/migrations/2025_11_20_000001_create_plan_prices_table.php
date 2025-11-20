<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->string('plan_id')->index();
            $table->string('plan_name');
            $table->string('plan_price');
            $table->string('plan_duration')->nullable();
            $table->text('plan_description')->nullable();
            $table->timestamps();

            // Add a composite index to speed lookups
            $table->index(['admin_id', 'plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_prices');
    }
};

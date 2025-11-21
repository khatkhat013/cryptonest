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
        Schema::table('plan_prices', function (Blueprint $table) {
            $table->string('crypto_screenshot')->nullable()->after('plan_description')->comment('Path to crypto payment screenshot');
            $table->string('mobile_screenshot')->nullable()->after('crypto_screenshot')->comment('Path to mobile payment screenshot');
            $table->string('payment_method')->nullable()->after('mobile_screenshot')->comment('Payment method: crypto or mobile_money');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_prices', function (Blueprint $table) {
            $table->dropColumn(['crypto_screenshot', 'mobile_screenshot', 'payment_method']);
        });
    }
};

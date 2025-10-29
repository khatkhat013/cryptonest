<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUserWalletsConstraints extends Migration
{
    public function up()
    {
        // For SQLite, we need to drop and recreate the table
        if (Schema::hasTable('user_wallets')) {
            // Get all existing data
            $wallets = DB::table('user_wallets')->get();
            
            // Drop the table
            Schema::dropIfExists('user_wallets');
            
            // Recreate table with correct structure
            Schema::create('user_wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('currency_id')->nullable();
                $table->string('coin')->nullable();
                $table->decimal('balance', 18, 8)->default(0);
                $table->timestamps();
                
                // Add foreign keys
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                    
                $table->foreign('currency_id')
                    ->references('id')
                    ->on('currencies')
                    ->onDelete('set null');
            });
            
            // Restore data
            foreach ($wallets as $wallet) {
                DB::table('user_wallets')->insert((array)$wallet);
            }
        }
    }

    public function down()
    {
        // For SQLite, just drop the table
        Schema::dropIfExists('user_wallets');
    }
}
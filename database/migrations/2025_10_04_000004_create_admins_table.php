<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->unique();
            $table->string('password');
            $table->string('telegram_username', 100)->unique();
            $table->boolean('is_approved')->default(false)->comment('Admin must be approved by site owner');
            $table->text('rejection_reason')->nullable()->comment('Reason if admin was rejected');
            $table->timestamp('approved_at')->nullable()->comment('When admin was approved');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Admin ID who approved this admin');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
};
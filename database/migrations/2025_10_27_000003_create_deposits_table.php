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
        // If a previous migration already created `deposits`, skip creation
        // to avoid duplicate table errors in mixed migration histories.
        if (Schema::hasTable('deposits')) {
            return;
        }

        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 16, 2);
            $table->foreignId('action_status_id')->constrained('action_statuses')->onDelete('restrict');
            $table->boolean('is_credited')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};

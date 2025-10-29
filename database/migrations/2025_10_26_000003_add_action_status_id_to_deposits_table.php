<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionStatusIdToDepositsTable extends Migration
{
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'action_status_id')) {
                $table->unsignedBigInteger('action_status_id')->nullable();
                $table->foreign('action_status_id')
                    ->references('id')
                    ->on('action_statuses')
                    ->onDelete('set null');
            }
            
            // Add more tracking fields
            if (!Schema::hasColumn('deposits', 'credited_at')) {
                $table->timestamp('credited_at')->nullable();
            }
            if (!Schema::hasColumn('deposits', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }
            if (!Schema::hasColumn('deposits', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['action_status_id', 'credited_at', 'approved_by', 'approved_at']);
        });
    }
}
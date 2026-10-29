<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->index('stock', 'items_stock_index');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->index(['status', 'due_date'], 'loans_status_due_date_index');
            $table->index('created_at', 'loans_created_at_index');
        });

        Schema::table('return_logs', function (Blueprint $table) {
            $table->index('returned_at', 'return_logs_returned_at_index');
            $table->index(['condition_on_return', 'returned_at'], 'return_logs_condition_returned_at_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'activity_logs_created_at_index');
            $table->index(['action', 'created_at'], 'activity_logs_action_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'name'], 'users_role_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_stock_index');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex('loans_status_due_date_index');
            $table->dropIndex('loans_created_at_index');
        });

        Schema::table('return_logs', function (Blueprint $table) {
            $table->dropIndex('return_logs_returned_at_index');
            $table->dropIndex('return_logs_condition_returned_at_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_index');
            $table->dropIndex('activity_logs_action_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_name_index');
        });
    }
};

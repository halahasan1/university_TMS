<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // TASKS
        Schema::table('tasks', function (Blueprint $table) {
            // charts + overdue + upcoming
            $table->index('status', 'tasks_status_idx');
            $table->index('due_date', 'tasks_due_date_idx');
            $table->index('created_at', 'tasks_created_at_idx');
            $table->index('updated_at', 'tasks_updated_at_idx');
            $table->index('assigned_to', 'tasks_assigned_to_idx');
            $table->index('created_by', 'tasks_created_by_idx');

            // overdue: status + due_date
            $table->index(['status', 'due_date'], 'tasks_status_due_date_idx');
        });

        // NEWS
        Schema::table('news', function (Blueprint $table) {
            $table->index('user_id', 'news_user_id_idx');
            $table->index('created_at', 'news_created_at_idx');
            $table->index('updated_at', 'news_updated_at_idx');
        });

        // PROFILES
        Schema::table('profiles', function (Blueprint $table) {
            $table->unique('user_id', 'profiles_user_id_unique');

            $table->index('department_id', 'profiles_department_id_idx');
            $table->index(['user_id', 'department_id'], 'profiles_user_department_idx');
        });

        // NOTIFICATIONS
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('created_at', 'notifications_created_at_idx');
            $table->index('read_at', 'notifications_read_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_status_idx');
            $table->dropIndex('tasks_due_date_idx');
            $table->dropIndex('tasks_created_at_idx');
            $table->dropIndex('tasks_updated_at_idx');
            $table->dropIndex('tasks_assigned_to_idx');
            $table->dropIndex('tasks_created_by_idx');
            $table->dropIndex('tasks_status_due_date_idx');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('news_user_id_idx');
            $table->dropIndex('news_created_at_idx');
            $table->dropIndex('news_updated_at_idx');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropUnique('profiles_user_id_unique');
            $table->dropIndex('profiles_department_id_idx');
            $table->dropIndex('profiles_user_department_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_created_at_idx');
            $table->dropIndex('notifications_read_at_idx');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('confirmation_queued_at')->nullable()->after('confirmation_sent_at');
            $table->timestamp('confirmation_failed_at')->nullable()->after('confirmation_queued_at');
            $table->timestamp('admin_notification_queued_at')->nullable()->after('confirmation_failed_at');
            $table->timestamp('admin_notification_sent_at')->nullable()->after('admin_notification_queued_at');
            $table->timestamp('admin_notification_failed_at')->nullable()->after('admin_notification_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'confirmation_queued_at',
                'confirmation_failed_at',
                'admin_notification_queued_at',
                'admin_notification_sent_at',
                'admin_notification_failed_at',
            ]);
        });
    }
};

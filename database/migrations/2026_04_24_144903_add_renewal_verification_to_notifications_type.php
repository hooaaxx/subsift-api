<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('renewal_alert', 'trial_expiry', 'renewal_verification') NOT NULL");
        } else {
            // For SQLite and other databases, change the column to allow the new value
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('renewal_alert', 'trial_expiry') NOT NULL");
        } else {
            // For SQLite and other databases, revert to the original enum-like string
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type')->change();
            });
        }
    }
};

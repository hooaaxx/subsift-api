<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('initials', 2);
            $table->string('color', 7);
            $table->decimal('cost', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->enum('payment_method', ['credit_card', 'app_store', 'carrier_billing']);
            $table->date('next_billing_date');
            $table->boolean('is_trial')->default(false);
            $table->date('trial_ends_at')->nullable();
            $table->boolean('alert_enabled')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'next_billing_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renewal_verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->enum('action', ['renewed', 'cancelled', 'price_changed']);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'used_at', 'expires_at'], 'idx_sub_used_exp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renewal_verification_tokens');
    }
};

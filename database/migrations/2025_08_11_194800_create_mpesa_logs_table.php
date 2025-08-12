<?php

// database/migrations/2025_08_11_000000_create_mpesa_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mpesa_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitee_id')->constrained()->cascadeOnDelete();

            $table->string('merchant_id')->nullable();
            $table->string('checkout_id')->nullable();
            $table->string('phone_number')->index();
            $table->string('status')->default('pending'); // '0' for queued/success-init per Safaricom, or strings
            $table->string('message')->nullable();

            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mpesa_logs');
    }
};
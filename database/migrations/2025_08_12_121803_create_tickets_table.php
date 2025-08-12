<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitee_id')->constrained()->cascadeOnDelete()->unique(); // 1 ticket per invitee
            $table->string('number')->unique(); // M-Pesa transaction_id
            $table->timestamp('issued_at')->nullable();
            $table->enum('status', ['Active', 'Used'])->default('Active'); // NEW
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
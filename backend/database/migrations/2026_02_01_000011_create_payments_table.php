<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('plan_id')->nullable();
            $table->string('provider', 20);
            $table->string('method', 10);
            $table->unsignedInteger('amount_cents');
            $table->char('currency', 3)->default('BRL');
            $table->string('status', 20)->default('pending')->index();
            $table->dateTime('paid_at')->nullable();
            $table->string('receipt_url', 2048)->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamps();

            $table->foreign('student_id', 'fk_payments_student_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('plan_id', 'fk_payments_plan_id')
                ->references('id')
                ->on('plans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

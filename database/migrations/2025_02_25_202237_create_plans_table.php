<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->string('stripe_monthly_plan_id')->nullable();
            $table->string('stripe_yearly_plan_id')->nullable();
            $table->string('paystack_monthly_plan_id')->nullable();
            $table->string('paystack_yearly_plan_id')->nullable();
            $table->integer('trial_days')->default(0);
            $table->integer('max_schools')->default(1);
            $table->integer('max_students')->default(100);
            $table->integer('max_teachers')->default(10);
            $table->integer('max_parents')->default(200);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

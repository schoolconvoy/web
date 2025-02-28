<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables that should have a tenant_id column.
     */
    protected array $tables = [
        'attendances',
        'assignments',
        'classes',
        'discounts',
        'discount_student_fee',
        'fees',
        'fee_categories',
        'invoices',
        'levels',
        'lesson_plans',
        'lesson_plan_reviews',
        'lesson_plan_topics',
        'libraries',
        'library_categories',
        'library_subcategories',
        'payments',
        'payment_reminders',
        'promotions',
        'results',
        'scholarships',
        'sessions',
        'subjects',
        'terms',
        'timetables',
        'waivers',
        'weeks',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                    $table->index('tenant_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropIndex(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};

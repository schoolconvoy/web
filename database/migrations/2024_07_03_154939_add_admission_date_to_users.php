<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('admission_date')->nullable();
        });
        $student_role = DB::table('roles')->where('name', 'student')->first(['id']);
        $student_role_id = $student_role->id;

        $student_users = DB::table('model_has_roles')
            ->where('role_id', $student_role_id)
            ->where('model_type', 'App\Models\User')
            ->pluck('model_id')->all();
        DB::table('users')
            ->whereIn('id', $student_users)
            ->update(['admission_date' => Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $student_role = DB::table('roles')->where('name', 'student')->first(['id']);
        $student_role_id = $student_role->id;

        $student_users = DB::table('model_has_roles')
            ->where('role_id', $student_role_id)
            ->where('model_type', 'App\Models\User')
            ->pluck('model_id')->all();
        DB::table('users')
            ->whereIn('id', $student_users)
            ->update(['admission_date' => null]);
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admission_date');
        });
    }
};

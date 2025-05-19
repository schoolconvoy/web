<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Fee;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $fees = Fee::all();
        foreach ($fees as $fee) {
            $fee->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

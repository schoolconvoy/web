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
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('category');
            $table->unsignedBigInteger('subcategory')->nullable();
            $table->string('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('file')->nullable();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('year')->nullable();
            $table->string('edition')->nullable();
            $table->string('isbn')->nullable();
            $table->string('pages')->nullable();
            $table->string('language');
            $table->string('count');
            $table->string('added_by');
            $table->timestamps();
        });

        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('library_subcategories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('libraries_category_subcategory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category');
            $table->unsignedBigInteger('subcategory');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};

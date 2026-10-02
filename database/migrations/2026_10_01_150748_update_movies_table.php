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
        Schema::table('movies', function (Blueprint $table) {
            $table->decimal('rating')->nullable()->after('duration');
            $table->unsignedBigInteger('tmdb_id')->after('id')->change();
            $table->string('backdrop_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('rating');
            $table->unsignedBigInteger('tmdb_id')->after('updated_at')->change();
            $table->dropColumn('backdrop_path');
        });
    }
};

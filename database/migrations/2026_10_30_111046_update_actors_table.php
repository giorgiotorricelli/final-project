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
        Schema::table('actors', function (Blueprint $table) {
            $table->date('birthday')->after('popularity')->nullable();
            $table->date('deathday')->after('birthday')->nullable();
            $table->string('place_of_birth', 100)->after('deathday')->nullable();
            $table->text('biography')->after('profile_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actors', function (Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('deathday');
            $table->dropColumn('place_of_birth');
            $table->dropColumn('biography');
        });
    }
};

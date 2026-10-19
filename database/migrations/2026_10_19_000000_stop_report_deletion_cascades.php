<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reports used to cascade-delete with anything they pointed at: deleting a
 * category or a city from the admin wiped every report filed under it, and
 * deleting an account took all of that citizen's reports with it.
 *
 * Reports are the public record, so from now on:
 * - categories and cities cannot be deleted while reports use them;
 * - deleting a district or quartier only clears that field on its reports;
 * - deleting an account keeps its reports, now shown as anonymous.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['quartier_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->restrictOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('quartier_id')->references('id')->on('quartiers')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['quartier_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
            $table->foreign('quartier_id')->references('id')->on('quartiers')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};

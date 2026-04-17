<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing districts with Salé city_id
        $saleCity = DB::table('cities')->where('name', 'Salé')->first();
        if ($saleCity) {
            DB::table('districts')->update([
                'city_id' => $saleCity->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('districts')->update([
            'city_id' => null
        ]);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Email verification became mandatory for publishing. Accounts that already
 * existed are treated as verified so nobody (admins included) is locked out
 * the moment the switch goes live; every account created from now on has to
 * confirm its address.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Irreversible on purpose: there is no record of which accounts were
        // verified for real before this ran.
    }
};

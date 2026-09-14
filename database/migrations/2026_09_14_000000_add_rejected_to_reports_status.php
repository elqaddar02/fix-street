<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow the REJECTED status that the admin panel already offers.
     */
    public function up(): void
    {
        $this->setAllowedStatuses(['OPEN', 'IN_PROGRESS', 'RESOLVED', 'REJECTED']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reports')->where('status', 'REJECTED')->update(['status' => 'OPEN']);

        $this->setAllowedStatuses(['OPEN', 'IN_PROGRESS', 'RESOLVED']);
    }

    private function setAllowedStatuses(array $statuses): void
    {
        $driver = Schema::getConnection()->getDriverName();
        $list = implode(', ', array_map(fn ($status) => "'{$status}'", $statuses));

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE reports MODIFY status ENUM({$list}) NOT NULL DEFAULT 'OPEN'");
        } elseif ($driver === 'pgsql') {
            // On PostgreSQL, Laravel stores enums as varchar with a "reports_status_check" constraint.
            DB::statement('ALTER TABLE reports DROP CONSTRAINT IF EXISTS reports_status_check');
            DB::statement("ALTER TABLE reports ADD CONSTRAINT reports_status_check CHECK (status IN ({$list}))");
        } else {
            Schema::table('reports', function (Blueprint $table) use ($statuses) {
                $table->enum('status', $statuses)->default('OPEN')->change();
            });
        }
    }
};

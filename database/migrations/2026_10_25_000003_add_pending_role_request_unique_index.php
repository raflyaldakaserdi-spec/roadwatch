<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "CREATE UNIQUE INDEX role_requests_one_pending_per_user
             ON role_requests (user_id)
             WHERE status = 'pending'"
        );
    }

    public function down(): void
    {
        DB::statement(
            "DROP INDEX IF EXISTS role_requests_one_pending_per_user"
        );
    }
};

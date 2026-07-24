<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->whereNotNull('delivery_street')
            ->update(['service_type' => 'delivery']);
    }

    public function down(): void
    {
        // No action required
    }
};

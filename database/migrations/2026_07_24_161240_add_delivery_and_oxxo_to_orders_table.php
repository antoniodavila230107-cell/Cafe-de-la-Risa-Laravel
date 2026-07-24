<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_street')->nullable()->after('service_type');
            $table->string('delivery_number')->nullable()->after('delivery_street');
            $table->string('delivery_neighborhood')->nullable()->after('delivery_number');
            $table->text('delivery_references')->nullable()->after('delivery_neighborhood');
            $table->string('oxxo_reference')->nullable()->after('card_last_four');
            $table->dateTime('oxxo_expires_at')->nullable()->after('oxxo_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_street',
                'delivery_number',
                'delivery_neighborhood',
                'delivery_references',
                'oxxo_reference',
                'oxxo_expires_at',
            ]);
        });
    }
};

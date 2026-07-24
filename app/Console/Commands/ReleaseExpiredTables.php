<?php

namespace App\Console\Commands;

use App\Models\Table;
use Illuminate\Console\Command;

class ReleaseExpiredTables extends Command
{
    protected $signature = 'cafe:release-expired-tables';
    protected $description = 'Libera mesas reservadas cuya reserva de 15 minutos ha expirado';

    public function handle(): int
    {
        $count = Table::where('status', 'reserved')
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<=', now())
            ->update([
                'status' => 'available',
                'reserved_until' => null,
                'current_order_folio' => null,
            ]);

        if ($count > 0) {
            $this->info("✓ Se liberaron {$count} reservas de mesa vencidas en MySQL.");
        }

        return Command::SUCCESS;
    }
}

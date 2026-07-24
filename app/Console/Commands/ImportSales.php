<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSales extends Command
{
    protected $signature = 'cafe:import-sales {path?}';
    protected $description = 'Importa ventas históricas de ventas.json a MySQL';

    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/legacy-import/ventas.json');

        if (!File::exists($path)) {
            $this->warn("El archivo {$path} no se encuentra. Omitiendo importación de ventas.");
            return Command::SUCCESS;
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        if (!$data || !is_array($data)) {
            $this->warn("El archivo de ventas está vacío o tiene un formato no válido.");
            return Command::SUCCESS;
        }

        $count = 0;
        DB::transaction(function () use ($data, &$count) {
            foreach ($data as $sale) {
                $folio = !empty($sale['Folio']) ? $sale['Folio'] : ('LEGACY-' . Str::random(6));
                $qrToken = !empty($sale['QrToken']) ? $sale['QrToken'] : Str::uuid()->toString();

                $order = Order::updateOrCreate(
                    ['folio' => $folio],
                    [
                        'customer_name' => $sale['ClienteNombre'] ?? 'Cliente General',
                        'customer_phone' => $sale['ClienteTelefono'] ?? null,
                        'customer_email' => $sale['ClienteCorreo'] ?? null,
                        'service_type' => 'para_llevar',
                        'subtotal' => $sale['Subtotal'] ?? ($sale['Total'] ?? 0),
                        'discount' => $sale['Descuento'] ?? 0,
                        'total' => $sale['Total'] ?? 0,
                        'payment_method' => strtolower($sale['MetodoPago'] ?? 'online') === 'efectivo' ? 'efectivo' : 'online',
                        'payment_status' => 'paid',
                        'order_status' => 'delivered',
                        'qr_token' => $qrToken,
                        'qr_used' => true,
                        'qr_used_at' => now(),
                        'created_at' => isset($sale['Fecha']) ? date('Y-m-d H:i:s', strtotime($sale['Fecha'])) : now(),
                    ]
                );

                OrderItem::where('order_id', $order->id)->delete();

                if (isset($sale['Detalles']) && is_array($sale['Detalles'])) {
                    foreach ($sale['Detalles'] as $detail) {
                        $product = Product::where('code', $detail['ProductoCodigo'] ?? '')->first();

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product?->id,
                            'item_code' => $detail['ProductoCodigo'] ?? 'P000',
                            'item_name' => $detail['ProductoNombre'] ?? 'Item',
                            'unit_price' => $detail['PrecioUnitario'] ?? 0,
                            'quantity' => $detail['Cantidad'] ?? 1,
                            'subtotal' => $detail['Subtotal'] ?? 0,
                        ]);
                    }
                }

                Payment::firstOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $order->payment_method,
                        'amount' => $order->total,
                        'reference' => 'IMPORTADO-LEGACY',
                        'status' => 'approved',
                    ]
                );

                $count++;
            }
        });

        $this->info("¡Éxito! Se importaron {$count} ventas históricas a MySQL.");
        return Command::SUCCESS;
    }
}

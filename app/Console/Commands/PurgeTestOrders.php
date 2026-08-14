<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeTestOrders extends Command
{
    private const CONFIRMATION = 'DELETE-ALL-TEST-ORDERS';

    protected $signature = 'orders:purge-test
        {--execute : Actually delete the orders; without this flag the command is preview-only}
        {--confirm= : Required confirmation phrase when executing}
        {--reset-identities : Reset order/order-item numeric IDs after deletion}
        {--force : Required when executing in production}';

    protected $description = 'Preview or permanently remove pre-production test orders while restoring unreleased reserved stock';

    public function handle(): int
    {
        $summary = $this->summary();

        $this->newLine();
        $this->info('MTD Art — test order cleanup');
        $this->table(
            ['Orders', 'Order items', 'Order value', 'Stock units to restore'],
            [[
                $summary['orders'],
                $summary['items'],
                number_format($summary['value'], 2, ',', '.').' RON',
                $summary['stock_units'],
            ]],
        );

        if ($summary['orders'] === 0) {
            $this->info('There are no orders to remove.');

            return self::SUCCESS;
        }

        if (! $this->option('execute')) {
            $this->warn('Preview only. Nothing was changed.');
            $this->line('To execute the cleanup, run:');
            $this->line(sprintf(
                'php artisan orders:purge-test --execute --confirm=%s --reset-identities%s',
                self::CONFIRMATION,
                app()->environment('production') ? ' --force' : '',
            ));

            return self::SUCCESS;
        }

        if (! hash_equals(self::CONFIRMATION, (string) $this->option('confirm'))) {
            $this->error('Cleanup refused: the confirmation phrase is missing or incorrect.');
            $this->line('Required: --confirm='.self::CONFIRMATION);

            return self::FAILURE;
        }

        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Cleanup refused in production without --force.');

            return self::FAILURE;
        }

        $this->warn('This permanently deletes every order currently in the database.');
        $this->warn('Products/categories are preserved. Only stock still marked as reserved and unreleased is restored.');

        $restoredUnits = 0;

        DB::transaction(function () use (&$restoredUnits): void {
            Order::query()->lockForUpdate()->get(['id']);

            $restocks = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereNotNull('orders.stock_reserved_at')
                ->whereNull('orders.stock_released_at')
                ->whereNotNull('order_items.product_id')
                ->groupBy('order_items.product_id')
                ->selectRaw('order_items.product_id, SUM(order_items.quantity) AS quantity')
                ->get();

            foreach ($restocks as $restock) {
                $quantity = (int) $restock->quantity;

                if ($quantity < 1) {
                    continue;
                }

                $updated = DB::table('products')
                    ->where('id', (int) $restock->product_id)
                    ->increment('stock', $quantity);

                if ($updated > 0) {
                    $restoredUnits += $quantity;
                }
            }

            if ($this->option('reset-identities') && DB::getDriverName() === 'pgsql') {
                DB::statement('TRUNCATE TABLE orders RESTART IDENTITY CASCADE');

                return;
            }

            Order::query()->delete();

            if ($this->option('reset-identities')) {
                $this->resetIdentitiesForNonPostgres();
            }
        });

        $this->newLine();
        $this->info("Deleted {$summary['orders']} test order(s) and restored {$restoredUnits} stock unit(s).");

        if ($this->option('reset-identities')) {
            $this->info('Order identities were reset for a clean production start.');
        }

        return self::SUCCESS;
    }

    /** @return array{orders:int,items:int,value:float,stock_units:int} */
    private function summary(): array
    {
        return [
            'orders' => Order::query()->count(),
            'items' => DB::table('order_items')->count(),
            'value' => (float) Order::query()->sum('total_amount'),
            'stock_units' => (int) DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereNotNull('orders.stock_reserved_at')
                ->whereNull('orders.stock_released_at')
                ->whereNotNull('order_items.product_id')
                ->sum('order_items.quantity'),
        ];
    }

    private function resetIdentitiesForNonPostgres(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::table('sqlite_sequence')
                ->whereIn('name', ['orders', 'order_items'])
                ->delete();

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE order_items AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1');

            return;
        }

        $this->warn("Identity reset is not implemented for database driver '{$driver}'. Orders were still deleted safely.");
    }
}

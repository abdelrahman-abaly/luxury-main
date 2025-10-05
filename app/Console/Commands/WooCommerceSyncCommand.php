<?php

namespace App\Console\Commands;

use App\Services\WooCommerceService;
use Illuminate\Console\Command;

class WooCommerceSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync
                            {type : Type of sync (products|orders|all)}
                            {--direction=both : Direction of sync (from-woo|to-woo|both)}
                            {--page=1 : Page number for WooCommerce API}
                            {--per-page=10 : Number of items per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize data between Laravel and WooCommerce';

    protected $wooCommerceService;

    public function __construct(WooCommerceService $wooCommerceService)
    {
        parent::__construct();
        $this->wooCommerceService = $wooCommerceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $direction = $this->option('direction');
        $page = $this->option('page');
        $perPage = $this->option('per-page');

        $this->info("Starting WooCommerce synchronization...");
        $this->info("Type: {$type}, Direction: {$direction}");

        try {
            switch ($type) {
                case 'products':
                    $this->syncProducts($direction, $page, $perPage);
                    break;
                case 'orders':
                    $this->syncOrders($direction, $page, $perPage);
                    break;
                case 'all':
                    $this->syncAll($direction, $page, $perPage);
                    break;
                default:
                    $this->error("Invalid sync type. Use: products, orders, or all");
                    return 1;
            }

            $this->info("Synchronization completed successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Synchronization failed: " . $e->getMessage());
            return 1;
        }
    }

    protected function syncProducts($direction, $page, $perPage)
    {
        if ($direction === 'from-woo' || $direction === 'both') {
            $this->info("Syncing products from WooCommerce...");
            $count = $this->wooCommerceService->syncProductsFromWooCommerce($page, $perPage);
            $this->info("Synced {$count} products from WooCommerce");
        }

        if ($direction === 'to-woo' || $direction === 'both') {
            $this->info("Syncing products to WooCommerce...");
            $count = $this->wooCommerceService->syncProductsToWooCommerce();
            $this->info("Synced {$count} products to WooCommerce");
        }
    }

    protected function syncOrders($direction, $page, $perPage)
    {
        if ($direction === 'from-woo' || $direction === 'both') {
            $this->info("Syncing orders from WooCommerce...");
            $count = $this->wooCommerceService->syncOrdersFromWooCommerce($page, $perPage);
            $this->info("Synced {$count} orders from WooCommerce");
        }

        if ($direction === 'to-woo' || $direction === 'both') {
            $this->info("Syncing orders to WooCommerce...");
            $count = $this->wooCommerceService->syncOrdersToWooCommerce();
            $this->info("Synced {$count} orders to WooCommerce");
        }
    }

    protected function syncAll($direction, $page, $perPage)
    {
        $this->syncProducts($direction, $page, $perPage);
        $this->syncOrders($direction, $page, $perPage);
    }
}

<?php

use App\Http\Controllers\CallsController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\EmailsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PerformancesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\RepairingOrderController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WooCommerceController;
use App\Http\Controllers\WooCommerceWebhookController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\WarehouseNotificationsController;
use App\Http\Controllers\WarehouseReportsController;
use App\Http\Controllers\WarehouseAnalyticsController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TreasuryController;

use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
 *          Routes Needs USer LOGIN
 * */
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
     *
     *      Routes needs permission CHECK
     *
     * */

    /*
     *          ROLES ROUTES
     *
     * */
    Route::prefix('roles')->group(function () {
        Route::get('/view', [RolesController::class, 'index'])->name('roles.index')->middleware("permission:roles,view");
        Route::get('/edit/{id}', [RolesController::class, 'edit'])->name('roles.edit')->middleware("permission:roles,edit");
        Route::get('/create', [RolesController::class, 'create'])->name('roles.create')->middleware("permission:roles,create");

        Route::post('/store-role', [RolesController::class, 'store'])->name('roles.store');
        Route::post('/update-role', [RolesController::class, 'update'])->name('roles.update');
        Route::post('/delete-role', [RolesController::class, 'destroy'])->name('roles.delete');
    });

    /*
     *          USERS ROUTES
     *
     * */
    Route::prefix('users')->group(function () {
        Route::get('/view', [UsersController::class, 'index'])->name('users.index')->middleware("permission:users,view");
        Route::get('/edit/{id}', [UsersController::class, 'edit'])->name('users.edit')->middleware("permission:users,edit");
        Route::get('/create', [UsersController::class, 'create'])->name('users.create')->middleware("permission:users,create");

        Route::post('/store-user', [UsersController::class, 'store'])->name('users.store');
        Route::post('/update-user', [UsersController::class, 'update'])->name('users.update');
        Route::post('/delete-user', [UsersController::class, 'destroy'])->name('users.delete');
    });

    /*
     *          ORDERS ROUTES
     *
     * */
    Route::prefix('orders')->group(function () {
        Route::get('/orders-list', [OrdersController::class, 'ordersList'])->name('orders.list')->middleware("permission:orders,view");
        Route::get('/edit/{id}', [OrdersController::class, 'edit'])->name('orders.edit')->middleware("permission:orders,edit");
        Route::get('/create', [OrdersController::class, 'create'])->name('orders.create')->middleware("permission:orders,create");
        // Route::get('/repairing-orders', [OrdersController::class,'repairingOrders'])->name('orders.repairing-orders')->middleware("permission:orders,view");

        Route::get('/repairing-orders', [RepairingOrderController::class, 'index'])
            ->name('orders.repairing-orders');
        Route::post('/repairing-orders', [RepairingOrderController::class, 'store'])
            ->name('orders.repairing-orders.store');
        Route::get('/repairing-orders/search', [RepairingOrderController::class, 'searchByPhone'])
            ->name('orders.repairing-orders.search');


        //
        Route::post('/store-order', [OrdersController::class, 'store'])->name('orders.store');
        //        Route::post('/update-order', [OrdersController::class,'update'])->name('orders.update');
        //        Route::post('/delete-order', [OrdersController::class,'destroy'])->name('orders.delete');
    });

    /*
     *          LEADS ROUTES
     *
     * */
    Route::prefix('leads')->group(function () {
        Route::get('/leads-list', [LeadsController::class, 'index'])->name('leads.index')->middleware("permission:leads,view");
        Route::get('/scheduled-tasks', [LeadsController::class, 'tasks'])->name('leads.tasks')->middleware("permission:leads,view");
        Route::get('/leads-reports', [LeadsController::class, 'reports'])->name('leads.reports')->middleware("permission:leads,view");
        Route::get('/edit/{id}', [LeadsController::class, 'edit'])->name('leads.edit')->middleware("permission:leads,edit");
        Route::get('/create', [LeadsController::class, 'create'])->name('leads.create')->middleware("permission:leads,create");
        Route::get('/create-task', [LeadsController::class, 'createTask'])->name('leads.create-task')->middleware("permission:leads,create");

        Route::post('/store-lead', [LeadsController::class, 'store'])->name('leads.store');
        Route::post('/update-lead', [LeadsController::class, 'update'])->name('leads.update');
        Route::post('/delete-lead', [LeadsController::class, 'destroy'])->name('leads.delete');
        Route::post('/task-done', [LeadsController::class, 'taskDone'])->name('leads.task-done');
        Route::post('/store-task', [LeadsController::class, 'storeTask'])->name('leads.store-task');
    });

    /*
     *          PRODUCTS ROUTES
     *
     * */
    Route::prefix('products')->group(function () {
        Route::get('/products-list', [ProductsController::class, 'index'])->name('products.index')->middleware("permission:products,view");
        //        Route::get('/scheduled-tasks', [LeadsController::class,'tasks'])->name('leads.tasks')->middleware("permission:leads,view");
        //        Route::get('/leads-reports', [LeadsController::class,'reports'])->name('leads.reports')->middleware("permission:leads,view");
        //        Route::get('/edit/{id}', [LeadsController::class,'edit'])->name('leads.edit')->middleware("permission:leads,edit");
        //        Route::get('/create', [LeadsController::class,'create'])->name('leads.create')->middleware("permission:leads,create");
        //        Route::get('/create-task', [LeadsController::class,'createTask'])->name('leads.create-task')->middleware("permission:leads,create");
        //
        //        Route::post('/store-lead', [LeadsController::class,'store'])->name('leads.store');
        //        Route::post('/update-lead', [LeadsController::class,'update'])->name('leads.update');
        //        Route::post('/delete-lead', [LeadsController::class,'destroy'])->name('leads.delete');
        //        Route::post('/task-done', [LeadsController::class,'taskDone'])->name('leads.task-done');
        //        Route::post('/store-task', [LeadsController::class,'storeTask'])->name('leads.store-task');
    });

    /*
     *
     *          Performances ROUTES
     * */
    Route::prefix('my-performance')->group(function () {
        Route::get("show", [PerformancesController::class, 'show'])->name('my-performance.show');
    });

    /*
    *
    *          Wallets ROUTES
    * */
    Route::prefix('my-wallet')->group(function () {
        Route::get("show", [WalletsController::class, 'show'])->name('my-wallet.show');
        Route::get("salary-calc", [WalletsController::class, 'salaryCalculate'])->name('my-wallet.salary-calc');
        Route::post("post-salary-calc", [WalletsController::class, 'postSalaryCalculate'])->name('my-wallet.post-salary-calc');
        Route::post("submit-borrow-request", [WalletsController::class, 'borrowRequest'])->name('my-wallet.submit-borrow-request');
        Route::post("submit-commission-withdrawal-request", [WalletsController::class, 'commissionWithdrawalRequest'])->name('my-wallet.submit-commission-withdrawal-request');
        Route::post("approve-request", [WalletsController::class, 'approveRequest'])->name('my-wallet.approve-request');
        Route::post("reject-request", [WalletsController::class, 'rejectRequest'])->name('my-wallet.reject-request');
    });

    /*
    *
    *          Calls ROUTES
    * */
    Route::prefix('calls')->group(function () {
        Route::get("show", [CallsController::class, 'show'])->name('calls.show');
    });

    /*
    *
    *          Chats ROUTES
    * */
    Route::prefix('chats')->group(function () {
        Route::get("show", [ChatsController::class, 'show'])->name('chats.show');
    });

    /*
    *
    *          Emails ROUTES
    * */
    Route::prefix('emails')->group(function () {
        Route::get("show", [EmailsController::class, 'show'])->name('emails.show');
    });

    /*
    *
    *          WAREHOUSE ROUTES
    * */
    Route::prefix('warehouse')->group(function () {
        Route::get('/home', [WarehouseController::class, 'home'])->name('warehouse.home')->middleware("permission:warehouse,view");

        // Orders section
        Route::get('/waiting-orders', [WarehouseController::class, 'waitingOrders'])->name('warehouse.waiting-orders')->middleware("permission:warehouse_orders,view");
        Route::get('/waiting-purchases', [WarehouseController::class, 'waitingPurchases'])->name('warehouse.waiting-purchases')->middleware("permission:warehouse_orders,view");
        Route::get('/accepted-orders', [WarehouseController::class, 'acceptedOrders'])->name('warehouse.accepted-orders')->middleware("permission:warehouse_orders,view");

        // Order actions
        Route::post('/accept-order/{orderId}', [WarehouseController::class, 'acceptOrder'])->name('warehouse.accept-order')->middleware("permission:warehouse_orders,edit");
        Route::post('/reject-order/{orderId}', [WarehouseController::class, 'rejectOrder'])->name('warehouse.reject-order')->middleware("permission:warehouse_orders,edit");
        Route::post('/ready-for-delivery/{orderId}', [WarehouseController::class, 'markReadyForDelivery'])->name('warehouse.ready-for-delivery')->middleware("permission:warehouse_orders,edit");

        // Send to Move Manager section
        Route::get('/waiting-send', [WarehouseController::class, 'waitingSend'])->name('warehouse.waiting-send')->middleware("permission:warehouse_move,view");
        Route::post('/assign-driver/{orderId}', [WarehouseController::class, 'assignDriver'])->name('warehouse.assign-driver')->middleware("permission:warehouse_move,edit");
        Route::post('/bulk-assign-driver', [WarehouseController::class, 'bulkAssignDriver'])->name('warehouse.bulk-assign-driver')->middleware("permission:warehouse_move,edit");

        Route::get('/order-sent', [WarehouseController::class, 'orderSent'])->name('warehouse.order-sent')->middleware("permission:warehouse_move,view");
        Route::post('/reassign-driver/{orderId}', [WarehouseController::class, 'reassignDriver'])->name('warehouse.reassign-driver')->middleware("permission:warehouse_move,edit");

        // Returns section
        Route::get('/waiting-returns', [WarehouseController::class, 'waitingReturns'])->name('warehouse.waiting-returns')->middleware("permission:warehouse_returns,view");
        Route::get('/returns-requests', [WarehouseController::class, 'returnsRequests'])->name('warehouse.returns-requests')->middleware("permission:warehouse_returns,view");
        Route::get('/accepted-returns', [WarehouseController::class, 'acceptedReturns'])->name('warehouse.accepted-returns')->middleware("permission:warehouse_returns,view");

        // Returns management actions
        Route::patch('/approve-return/{returnId}', [WarehouseController::class, 'approveReturn'])->name('warehouse.approve-return')->middleware('auth');
        Route::patch('/reject-return/{returnId}', [WarehouseController::class, 'rejectReturn'])->name('warehouse.reject-return')->middleware('auth');
        Route::post('/process-return/{orderId}', [WarehouseController::class, 'processReturn'])->name('warehouse.process-return')->middleware("permission:warehouse_returns,update");
        Route::post('/complete-return/{orderId}', [WarehouseController::class, 'completeReturn'])->name('warehouse.complete-return')->middleware("permission:warehouse_returns,update");

        // Feeding Requests section
        Route::get('/feeding-requests', [WarehouseController::class, 'feedingRequests'])->name('warehouse.feeding-requests')->middleware("permission:warehouse_feeding,view");
        Route::get('/exit-permission', [WarehouseController::class, 'exitPermission'])->name('warehouse.exit-permission')->middleware("permission:warehouse_feeding,view");
        Route::get('/exit-permission/{orderId}/details', [WarehouseController::class, 'exitPermissionDetails'])->name('warehouse.exit-permission-details')->middleware("permission:warehouse_feeding,view");
        Route::post('/process-feeding-request/{orderId}', [WarehouseController::class, 'processFeedingRequest'])->name('warehouse.process-feeding-request')->middleware('auth');
        Route::post('/create-feeding-request', [WarehouseController::class, 'createFeedingRequest'])->name('warehouse.create-feeding-request')->middleware("permission:warehouse_feeding,create");
        Route::post('/create-exit-permission', [WarehouseController::class, 'createExitPermission'])->name('warehouse.create-exit-permission')->middleware("permission:warehouse_feeding,create");
        Route::get('/api/orders/{orderNumber}', [WarehouseController::class, 'getOrderByNumber'])->name('warehouse.get-order-by-number');
        Route::get('/api/orders/{orderId}/products', [WarehouseController::class, 'getOrderProducts'])->name('warehouse.get-order-products');
        Route::get('/api/test-order-products/{orderId}', function ($orderId) {
            try {
                $order = \App\Models\Order::find($orderId);
                if (!$order) {
                    return response()->json(['error' => 'Order not found'], 404);
                }
                return response()->json([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'products_count' => $order->products()->count(),
                    'products' => $order->products()->get(['id', 'name', 'sku'])
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
        Route::post('/process-exit-permission/{orderId}', [WarehouseController::class, 'processExitPermission'])->name('warehouse.process-exit-permission');
        Route::post('/bulk-process-exit-permissions', [WarehouseController::class, 'bulkProcessExitPermissions'])->name('warehouse.bulk-process-exit-permissions')->middleware("permission:warehouse_feeding,update");

        // Stock section
        Route::get('/in-stock', [WarehouseController::class, 'inStock'])->name('warehouse.in-stock')->middleware("permission:warehouse_stock,view");
        Route::get('/almost-out-stock', [WarehouseController::class, 'almostOutOfStock'])->name('warehouse.almost-out-stock')->middleware("permission:warehouse_stock,view");
        Route::get('/out-of-stock', [WarehouseController::class, 'outOfStock'])->name('warehouse.out-of-stock')->middleware("permission:warehouse_stock,view");

        // Stock management actions
        Route::post('/update-stock/{productId}', [WarehouseController::class, 'updateStock'])->name('warehouse.update-stock')->middleware("permission:warehouse_stock,update");
        Route::post('/bulk-update-stock', [WarehouseController::class, 'bulkUpdateStock'])->name('warehouse.bulk-update-stock')->middleware("permission:warehouse_stock,update");

        // Materials section
        Route::get('/boxes', [WarehouseController::class, 'boxes'])->name('warehouse.boxes')->middleware("permission:warehouse_materials,view");
        Route::get('/boxes/{id}', [WarehouseController::class, 'getBox'])->name('warehouse.get-box')->middleware("permission:warehouse_materials,view");
        Route::post('/boxes', [WarehouseController::class, 'storeBox'])->name('warehouse.store-box')->middleware("permission:warehouse_materials,create");
        Route::put('/boxes/{id}', [WarehouseController::class, 'updateBox'])->name('warehouse.update-box')->middleware("permission:warehouse_materials,update");
        Route::delete('/boxes/{id}', [WarehouseController::class, 'destroyBox'])->name('warehouse.destroy-box')->middleware("permission:warehouse_materials,delete");
        Route::patch('/boxes/{id}/stock', [WarehouseController::class, 'updateBoxStock'])->name('warehouse.update-box-stock')->middleware("permission:warehouse_materials,update");
        Route::get('/shopping-bags', [WarehouseController::class, 'shoppingBags'])->name('warehouse.shopping-bags')->middleware("permission:warehouse_materials,view");
        Route::get('/shopping-bags/{id}', [WarehouseController::class, 'getShoppingBag'])->name('warehouse.get-shopping-bag')->middleware("permission:warehouse_materials,view");
        Route::post('/shopping-bags', [WarehouseController::class, 'storeShoppingBag'])->name('warehouse.store-shopping-bag')->middleware("permission:warehouse_materials,create");
        Route::put('/shopping-bags/{id}', [WarehouseController::class, 'updateShoppingBag'])->name('warehouse.update-shopping-bag')->middleware("permission:warehouse_materials,update");
        Route::delete('/shopping-bags/{id}', [WarehouseController::class, 'destroyShoppingBag'])->name('warehouse.destroy-shopping-bag')->middleware("permission:warehouse_materials,delete");
        Route::patch('/shopping-bags/{id}/stock', [WarehouseController::class, 'updateShoppingBagStock'])->name('warehouse.update-shopping-bag-stock')->middleware("permission:warehouse_materials,update");

        // Debug route
        Route::get('/debug/shopping-bags', function () {
            $bags = \App\Models\Product::where('category', 'Shopping Bags')->orderBy('created_at', 'desc')->take(5)->get();
            return response()->json([
                'total_bags' => \App\Models\Product::where('category', 'Shopping Bags')->count(),
                'recent_bags' => $bags->map(function ($bag) {
                    return [
                        'id' => $bag->id,
                        'name' => $bag->name,
                        'sku' => $bag->sku,
                        'created_at' => $bag->created_at
                    ];
                })
            ]);
        });
        Route::get('/prime-bags', [WarehouseController::class, 'primeBags'])->name('warehouse.prime-bags')->middleware("permission:warehouse_materials,view");
        Route::get('/prime-bags/{id}', [WarehouseController::class, 'getPrimeBag'])->name('warehouse.get-prime-bag')->middleware("permission:warehouse_materials,view");
        Route::post('/prime-bags', [WarehouseController::class, 'storePrimeBag'])->name('warehouse.store-prime-bag')->middleware("permission:warehouse_materials,create");
        Route::put('/prime-bags/{id}', [WarehouseController::class, 'updatePrimeBag'])->name('warehouse.update-prime-bag')->middleware("permission:warehouse_materials,update");
        Route::delete('/prime-bags/{id}', [WarehouseController::class, 'destroyPrimeBag'])->name('warehouse.destroy-prime-bag')->middleware("permission:warehouse_materials,delete");
        Route::patch('/prime-bags/{id}/stock', [WarehouseController::class, 'updatePrimeBagStock'])->name('warehouse.update-prime-bag-stock')->middleware("permission:warehouse_materials,update");
        Route::get('/flyerz', [WarehouseController::class, 'flyerz'])->name('warehouse.flyerz')->middleware("permission:warehouse_materials,view");
        Route::get('/flyerz/{id}', [WarehouseController::class, 'getFlyer'])->name('warehouse.get-flyer')->middleware("permission:warehouse_materials,view");
        Route::post('/flyerz', [WarehouseController::class, 'storeFlyer'])->name('warehouse.store-flyer')->middleware("permission:warehouse_materials,create");
        Route::put('/flyerz/{id}', [WarehouseController::class, 'updateFlyer'])->name('warehouse.update-flyer')->middleware("permission:warehouse_materials,update");
        Route::delete('/flyerz/{id}', [WarehouseController::class, 'destroyFlyer'])->name('warehouse.destroy-flyer')->middleware("permission:warehouse_materials,delete");
        Route::patch('/flyerz/{id}/stock', [WarehouseController::class, 'updateFlyerStock'])->name('warehouse.update-flyer-stock')->middleware("permission:warehouse_materials,update");

        // Materials management actions
        Route::post('/update-material-stock/{materialId}', [WarehouseController::class, 'updateMaterialStock'])->name('warehouse.update-material-stock')->middleware("permission:warehouse_materials,update");
        Route::post('/bulk-update-materials-stock', [WarehouseController::class, 'bulkUpdateMaterialsStock'])->name('warehouse.bulk-update-materials-stock')->middleware("permission:warehouse_materials,update");

        // Notifications routes
        Route::prefix('notifications')->middleware(['auth'])->group(function () {
            Route::get('/', [WarehouseNotificationsController::class, 'index'])->name('warehouse.notifications.index');
            Route::post('/mark-all-read', [WarehouseNotificationsController::class, 'markAllAsRead'])->name('warehouse.notifications.mark-all-read');
            Route::post('/clear-all', [WarehouseNotificationsController::class, 'clearAll'])->name('warehouse.notifications.clear-all');
            Route::post('/{id}/mark-read', [WarehouseNotificationsController::class, 'markAsRead'])->name('warehouse.notifications.mark-read');
        });

        // Reports routes
        Route::prefix('reports')->middleware(['auth'])->group(function () {
            Route::get('/', [WarehouseReportsController::class, 'index'])->name('warehouse.reports.index');
            Route::get('/stock-movement', [WarehouseReportsController::class, 'stockMovement'])->name('warehouse.reports.stock-movement');
            Route::get('/damaged-items', [WarehouseReportsController::class, 'damagedItems'])->name('warehouse.reports.damaged-items');
            Route::get('/returns', [WarehouseReportsController::class, 'returns'])->name('warehouse.reports.returns');
            Route::get('/repairing-orders', [WarehouseReportsController::class, 'repairingOrders'])->name('warehouse.reports.repairing-orders');
            Route::get('/performance', [WarehouseReportsController::class, 'performance'])->name('warehouse.reports.performance');
            Route::get('/export', [WarehouseReportsController::class, 'export'])->name('warehouse.reports.export');
        });

        // Analytics routes
        Route::prefix('analytics')->middleware(['auth'])->group(function () {
            Route::get('/', [WarehouseAnalyticsController::class, 'index'])->name('warehouse.analytics.index');
        });

        // Damaged section
        Route::get('/damaged-goods', [WarehouseController::class, 'damagedGoods'])->name('warehouse.damaged-goods')->middleware("permission:warehouse_damaged,view");
        Route::get('/damaged-materials', [WarehouseController::class, 'damagedMaterials'])->name('warehouse.damaged-materials')->middleware("permission:warehouse_damaged,view");
        Route::post('/mark-as-damaged', [WarehouseController::class, 'markAsDamaged'])->name('warehouse.mark-as-damaged')->middleware("permission:warehouse_damaged,create");
        Route::get('/available-products', [WarehouseController::class, 'getAvailableProducts'])->name('warehouse.available-products')->middleware("permission:warehouse_damaged,view");
        Route::patch('/damaged-items/{id}/status', [WarehouseController::class, 'updateDamagedItemStatus'])->name('warehouse.update-damaged-item-status')->middleware("permission:warehouse_damaged,update");
    });

    /*
     *          WOOCOMMERCE ROUTES
     *
     * */
    Route::prefix('woocommerce')->group(function () {
        Route::get('/manage', function () {
            return view('woocommerce.index');
        })->name('woocommerce.manage');
        Route::get('/test-connection', [WooCommerceController::class, 'testConnection'])->name('woocommerce.test-connection');
        Route::post('/sync-products-from-woo', [WooCommerceController::class, 'syncProductsFromWooCommerce'])->name('woocommerce.sync-products-from-woo');
        Route::post('/sync-products-to-woo', [WooCommerceController::class, 'syncProductsToWooCommerce'])->name('woocommerce.sync-products-to-woo');
        Route::post('/sync-orders-from-woo', [WooCommerceController::class, 'syncOrdersFromWooCommerce'])->name('woocommerce.sync-orders-from-woo');
        Route::post('/sync-orders-to-woo', [WooCommerceController::class, 'syncOrdersToWooCommerce'])->name('woocommerce.sync-orders-to-woo');
        Route::post('/full-sync', [WooCommerceController::class, 'fullSync'])->name('woocommerce.full-sync');
    });

    /*
            *          IMPORT/EXPORT ROUTES
            *
            * */
    Route::prefix('import-export')->group(function () {
        Route::post('/export', [ImportExportController::class, 'export'])->name('import-export.export');
        Route::post('/import', [ImportExportController::class, 'import'])->name('import-export.import');
        Route::get('/template', [ImportExportController::class, 'downloadTemplate'])->name('import-export.template');
    });

    /*
     *          DRIVER ROUTES
     * */
    Route::prefix('driver')->group(function () {
        Route::get('/home', [DriverController::class, 'home'])->name('driver.home')->middleware("permission:driver,view");
        Route::get('/my-orders', [DriverController::class, 'myOrders'])->name('driver.my-orders')->middleware("permission:driver_orders,view");

        // Order actions
        Route::post('/accept-order/{orderId}', [DriverController::class, 'acceptOrder'])->name('driver.accept-order')->middleware("permission:driver_orders,edit");
        Route::post('/reject-order/{orderId}', [DriverController::class, 'rejectOrder'])->name('driver.reject-order')->middleware("permission:driver_orders,edit");
        Route::post('/mark-as-delivered/{orderId}', [DriverController::class, 'markAsDelivered'])->name('driver.mark-as-delivered')->middleware("permission:driver_orders,edit");
        Route::post('/mark-as-returned/{orderId}', [DriverController::class, 'markAsReturned'])->name('driver.mark-as-returned')->middleware("permission:driver_orders,edit");
    });

    /*
     *          TREASURY ROUTES
     * */
    Route::prefix('treasury')->group(function () {
        Route::get('/home', [TreasuryController::class, 'home'])->name('treasury.home')->middleware("permission:treasury,view");
        Route::get('/transactions', [TreasuryController::class, 'transactions'])->name('treasury.transactions')->middleware("permission:treasury,view");
        Route::post('/export', [TreasuryController::class, 'exportReport'])->name('treasury.export')->middleware("permission:treasury,export");
    });
});

/*
 *          WOOCOMMERCE WEBHOOK ROUTES (No authentication required)
 *
 * */
Route::post('/webhook/woocommerce', [WooCommerceWebhookController::class, 'handleWebhook'])->name('woocommerce.webhook');

require __DIR__ . '/auth.php';

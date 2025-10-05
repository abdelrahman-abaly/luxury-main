@extends("layouts.main")

@section("content")
<div id="woocommerce-management">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 dashboard-header">WooCommerce Integration</h2>
        <div class="d-flex">
            <button class="btn btn-outline-primary me-2" onclick="testConnection()">
                <i class="fas fa-plug"></i> Test Connection
            </button>
            <button class="btn btn-success" onclick="fullSync()">
                <i class="fas fa-sync"></i> Full Sync
            </button>
        </div>
    </div>

    <!-- Connection Status -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Connection Status</h5>
        </div>
        <div class="card-body">
            <div id="connection-status" class="alert alert-info">
                <i class="fas fa-info-circle"></i> Click "Test Connection" to check WooCommerce API connectivity
            </div>
        </div>
    </div>

    <!-- Sync Controls -->
    <div class="row">
        <!-- Products Sync -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Products Synchronization</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sync products between Laravel and WooCommerce</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="syncProductsFromWoo()">
                            <i class="fas fa-download"></i> Sync from WooCommerce
                        </button>
                        <button class="btn btn-secondary" onclick="syncProductsToWoo()">
                            <i class="fas fa-upload"></i> Sync to WooCommerce
                        </button>
                    </div>
                    <div id="products-sync-result" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Orders Sync -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Orders Synchronization</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sync orders between Laravel and WooCommerce</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="syncOrdersFromWoo()">
                            <i class="fas fa-download"></i> Sync from WooCommerce
                        </button>
                        <button class="btn btn-secondary" onclick="syncOrdersToWoo()">
                            <i class="fas fa-upload"></i> Sync to WooCommerce
                        </button>
                    </div>
                    <div id="orders-sync-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Sync Activity</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Status</th>
                            <th>Items Synced</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="sync-history">
                        <tr>
                            <td colspan="5" class="text-center text-muted">No sync activity yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Test WooCommerce connection
    async function testConnection() {
        const statusDiv = document.getElementById('connection-status');
        statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing connection...';
        statusDiv.className = 'alert alert-info';

        try {
            const response = await fetch('/woocommerce/test-connection', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Connection successful! WooCommerce API is accessible.';
                statusDiv.className = 'alert alert-success';
            } else {
                statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Connection failed: ' + result.message;
                statusDiv.className = 'alert alert-danger';
            }
        } catch (error) {
            statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Connection error: ' + error.message;
            statusDiv.className = 'alert alert-danger';
        }
    }

    // Sync products from WooCommerce
    async function syncProductsFromWoo() {
        const resultDiv = document.getElementById('products-sync-result');
        resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Syncing products from WooCommerce...</div>';

        try {
            const response = await fetch('/woocommerce/sync-products-from-woo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                resultDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Products', 'From WooCommerce', 'Success', result.synced_count);
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Products', 'From WooCommerce', 'Failed', 0);
            }
        } catch (error) {
            resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error: ${error.message}</div>`;
            addToSyncHistory('Products', 'From WooCommerce', 'Error', 0);
        }
    }

    // Sync products to WooCommerce
    async function syncProductsToWoo() {
        const resultDiv = document.getElementById('products-sync-result');
        resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Syncing products to WooCommerce...</div>';

        try {
            const response = await fetch('/woocommerce/sync-products-to-woo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                resultDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Products', 'To WooCommerce', 'Success', result.synced_count);
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Products', 'To WooCommerce', 'Failed', 0);
            }
        } catch (error) {
            resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error: ${error.message}</div>`;
            addToSyncHistory('Products', 'To WooCommerce', 'Error', 0);
        }
    }

    // Sync orders from WooCommerce
    async function syncOrdersFromWoo() {
        const resultDiv = document.getElementById('orders-sync-result');
        resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Syncing orders from WooCommerce...</div>';

        try {
            const response = await fetch('/woocommerce/sync-orders-from-woo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                resultDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Orders', 'From WooCommerce', 'Success', result.synced_count);
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Orders', 'From WooCommerce', 'Failed', 0);
            }
        } catch (error) {
            resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error: ${error.message}</div>`;
            addToSyncHistory('Orders', 'From WooCommerce', 'Error', 0);
        }
    }

    // Sync orders to WooCommerce
    async function syncOrdersToWoo() {
        const resultDiv = document.getElementById('orders-sync-result');
        resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Syncing orders to WooCommerce...</div>';

        try {
            const response = await fetch('/woocommerce/sync-orders-to-woo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                resultDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Orders', 'To WooCommerce', 'Success', result.synced_count);
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ${result.message}</div>`;
                addToSyncHistory('Orders', 'To WooCommerce', 'Failed', 0);
            }
        } catch (error) {
            resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error: ${error.message}</div>`;
            addToSyncHistory('Orders', 'To WooCommerce', 'Error', 0);
        }
    }

    // Full sync
    async function fullSync() {
        const statusDiv = document.getElementById('connection-status');
        statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Performing full synchronization...';
        statusDiv.className = 'alert alert-info';

        try {
            const response = await fetch('/woocommerce/full-sync', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Full synchronization completed successfully!';
                statusDiv.className = 'alert alert-success';

                // Update individual result divs
                document.getElementById('products-sync-result').innerHTML =
                    `<div class="alert alert-success">Products: ${result.results.products_from_woocommerce} from WooCommerce, ${result.results.products_to_woocommerce} to WooCommerce</div>`;
                document.getElementById('orders-sync-result').innerHTML =
                    `<div class="alert alert-success">Orders: ${result.results.orders_from_woocommerce} from WooCommerce, ${result.results.orders_to_woocommerce} to WooCommerce</div>`;
            } else {
                statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Full synchronization failed: ' + result.message;
                statusDiv.className = 'alert alert-danger';
            }
        } catch (error) {
            statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Full synchronization error: ' + error.message;
            statusDiv.className = 'alert alert-danger';
        }
    }

    // Add entry to sync history
    function addToSyncHistory(type, direction, status, count) {
        const historyTable = document.getElementById('sync-history');
        const now = new Date().toLocaleString();

        // Remove the "no activity" row if it exists
        const noActivityRow = historyTable.querySelector('tr td[colspan="5"]');
        if (noActivityRow) {
            noActivityRow.parentElement.remove();
        }

        const newRow = historyTable.insertRow(0);
        newRow.innerHTML = `
                <td>${type}</td>
                <td>${direction}</td>
                <td><span class="badge ${status === 'Success' ? 'bg-success' : 'bg-danger'}">${status}</span></td>
                <td>${count}</td>
                <td>${now}</td>
            `;
    }
</script>
@endsection

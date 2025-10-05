<!-- Sidebar -->

<style>
    /* .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #0d6efd;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 20px 0;
        } */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 0.7rem;
        font-weight: bold;
        min-width: 18px;
        text-align: center;
    }

    .nav-link-badge {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: auto;
    }
</style>
<div class="sidebar bg-primary text-white p-3">
    <div class="text-center mb-4">
        <img src="{{(auth()->user()->avatar != "" && auth()->user()->avatar != null)? Storage::url(auth()->user()->avatar) : 'https://randomuser.me/api/portraits/men/32.jpg'}}" alt="Profile" class="profile-img mb-2">
        <h5 class="mb-0">{{auth()->user()->name}}</h5>
        <small class="text-white-50">{{auth()->user()->role->role_name}}</small>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('home/*') ? 'active' : '' }}" href="{{route('home')}}">
                <i class="fas fa-home"></i> Home
            </a>
        </li>

        @if(auth()->user()->hasPermission('roles', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('roles/*') ? 'active' : '' }}" href="{{route('roles.index')}}">
                <i class="fas fa-key"></i> Roles
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('users', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('users/*') ? 'active' : '' }}" href="{{route('users.index')}}">
                <i class="fa-solid fa-users"></i> Users
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('orders', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#ordersCollapse">
                <i class="fas fa-shopping-cart"></i> Orders <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('orders/*') ? 'show' : '' }}" id="ordersCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('orders/orders-list') ? 'active' : '' }}" href="{{route('orders.list')}}">Orders List</a>
                    </li>
                    @if(auth()->user()->hasPermission('orders', 'create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('orders/create') ? 'active' : '' }}" href="{{route('orders.create')}}">Add New Order</a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('repairing_orders', 'create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('orders/repairing-orders') ? 'active' : '' }}" href="{{route('orders.repairing-orders')}}">Repairing Order</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif



        @if(auth()->user()->hasPermission('leads', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#leadsCollapse">
                <i class="fas fa-users"></i> Leads <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('leads/*') ? 'show' : '' }}" id="leadsCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('leads/leads-list') ? 'active' : '' }}" href="{{route('leads.index')}}">My Leads List</a>
                    </li>
                    @if(auth()->user()->hasPermission('leads', 'create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('leads/create') ? 'active' : '' }}" href="{{route('leads.create')}}">Add New Lead</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('leads/scheduled-tasks') || request()->is('leads/create-task') ? 'active' : '' }}" href="{{route('leads.tasks')}}">Scheduled Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('leads/leads-reports') ? 'active' : '' }}" href="{{route('leads.reports')}}">Leads Reports</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('products', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#ecommerceCollapse">
                <i class="fas fa-store"></i> Ecommerce <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('products/*') ? 'show' : '' }}" id="ecommerceCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products/*') ? 'active' : '' }}" href="{{route('products.index')}}">Products</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        <!-- @if(auth()->user()->hasPermission('chats', 'view'))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('chats/*') ? 'active' : '' }}" href="{{route('chats.show')}}">
                    <i class="fas fa-comments"></i> Chats
                </a>
            </li>
        @endif -->

        <!-- @if(auth()->user()->hasPermission('calls', 'view'))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('calls/*') ? 'active' : '' }}" href="{{route('calls.show')}}">
                    <i class="fas fa-phone"></i> Calls
                </a>
            </li>
        @endif -->

        @if(auth()->user()->hasPermission('emails', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('emails/*') ? 'active' : '' }}" href="{{route('emails.show')}}">
                <i class="fas fa-envelope"></i> Emails
            </a>
        </li>
        @endif

        {{-- WAREHOUSE MENU ITEMS --}}
        @if(auth()->user()->hasPermission('warehouse', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('warehouse/home') ? 'active' : '' }}" href="{{route('warehouse.home')}}">
                <i class="fas fa-warehouse"></i> Warehouse Home
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_orders', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseOrdersCollapse">
                <i class="fas fa-boxes"></i> Orders <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/waiting-orders') || request()->is('warehouse/waiting-purchases') || request()->is('warehouse/accepted-orders') ? 'show' : '' }}" id="warehouseOrdersCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/waiting-orders') ? 'active' : '' }}" href="{{route('warehouse.waiting-orders')}}"><span>Waiting Orders</span>
                            <span class="nav-link-badge">30</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/waiting-purchases') ? 'active' : '' }}" href="{{route('warehouse.waiting-purchases')}}"><span>Waiting for Purchases</span>
                            <span class="nav-link-badge">10</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/accepted-orders') ? 'active' : '' }}" href="{{route('warehouse.accepted-orders')}}"><span>Accepted Orders</span>
                            <span class="nav-link-badge">50</span></a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_move', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseMoveCollapse">
                <i class="fas fa-truck"></i> Send to Move Manager <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/waiting-send') || request()->is('warehouse/order-sent') ? 'show' : '' }}" id="warehouseMoveCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/waiting-send') ? 'active' : '' }}" href="{{route('warehouse.waiting-send')}}"><span>Waiting Send</span>
                            <span class="nav-link-badge">25</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/order-sent') ? 'active' : '' }}" href="{{route('warehouse.order-sent')}}"><span>Order Sent</span>
                            <span class="nav-link-badge">40</span></a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_returns', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseReturnsCollapse">
                <i class="fas fa-undo"></i> Returns <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/waiting-returns') || request()->is('warehouse/returns-requests') || request()->is('warehouse/accepted-returns') ? 'show' : '' }}" id="warehouseReturnsCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/waiting-returns') ? 'active' : '' }}" href="{{route('warehouse.waiting-returns')}}"><span>Waiting Returns</span>
                            <span class="nav-link-badge">30</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/returns-requests') ? 'active' : '' }}" href="{{route('warehouse.returns-requests')}}"><span>Returns Requests</span>
                            <span class="nav-link-badge">10</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/accepted-returns') ? 'active' : '' }}" href="{{route('warehouse.accepted-returns')}}"><span>Accepted Returns</span>
                            <span class="nav-link-badge">15</span></a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_feeding', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseFeedingCollapse">
                <i class="fas fa-clipboard-list"></i> Feeding Requests <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/feeding-requests') || request()->is('warehouse/exit-permission') ? 'show' : '' }}" id="warehouseFeedingCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/feeding-requests') ? 'active' : '' }}" href="{{route('warehouse.feeding-requests')}}"><span>Feeding Requests</span>
                            <span class="nav-link-badge bg-danger">1</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/exit-permission') ? 'active' : '' }}" href="{{route('warehouse.exit-permission')}}"><span>Exit Permission</span>
                            <span class="nav-link-badge bg-danger">1</span></a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_stock', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseStockCollapse">
                <i class="fas fa-cubes"></i> Stock <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/in-stock') || request()->is('warehouse/almost-out-stock') || request()->is('warehouse/out-of-stock') ? 'show' : '' }}" id="warehouseStockCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/in-stock') ? 'active' : '' }}" href="{{route('warehouse.in-stock')}}"><span>In Stock</span>
                            <span class="nav-link-badge">3000</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/almost-out-stock') ? 'active' : '' }}" href="{{route('warehouse.almost-out-stock')}}"><span>Almost Out of Stock</span>
                            <span class="nav-link-badge bg-warning">250</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/out-of-stock') ? 'active' : '' }}" href="{{route('warehouse.out-of-stock')}}"><span>Out of Stock</span>
                            <span class="nav-link-badge bg-danger">6000</span></a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_materials', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseMaterialsCollapse">
                <i class="fas fa-box"></i> Materials <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/boxes') || request()->is('warehouse/shopping-bags') || request()->is('warehouse/prime-bags') || request()->is('warehouse/flyerz') ? 'show' : '' }}" id="warehouseMaterialsCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/boxes') ? 'active' : '' }}" href="{{route('warehouse.boxes')}}"><span>Boxes</span>
                            <span class="nav-link-badge">4300</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/shopping-bags') ? 'active' : '' }}" href="{{route('warehouse.shopping-bags')}}"><span>Shopping Bags</span>
                            <span class="nav-link-badge">4000</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/prime-bags') ? 'active' : '' }}" href="{{route('warehouse.prime-bags')}}"><span>Prime Bags</span>
                            <span class="nav-link-badge">4000</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/flyerz') ? 'active' : '' }}" href="{{route('warehouse.flyerz')}}">Flyerz</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('warehouse_damaged', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#warehouseDamagedCollapse">
                <i class="fas fa-exclamation-triangle"></i> Damaged <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('warehouse/damaged-goods') || request()->is('warehouse/damaged-materials') ? 'show' : '' }}" id="warehouseDamagedCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/damaged-goods') ? 'active' : '' }}" href="{{route('warehouse.damaged-goods')}}"><span>Damaged Goods</span>
                            <span class="nav-link-badge bg-danger">327</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('warehouse/damaged-materials') ? 'active' : '' }}" href="{{route('warehouse.damaged-materials')}}"><span>Damaged Materials</span>
                            <span class="nav-link-badge bg-danger">280</span></a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->hasPermission('treasury', 'view'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#treasuryCollapse">
                <i class="fas fa-vault"></i> Treasury <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <div class="collapse {{ request()->is('treasury/home') || request()->is('treasury/transactions') ? 'show' : '' }}" id="treasuryCollapse">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('treasury/home') ? 'active' : '' }}" href="{{route('treasury.home')}}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('treasury/transactions') ? 'active' : '' }}" href="{{route('treasury.transactions')}}">
                            <i class="fas fa-receipt"></i> All Transactions
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        {{-- DRIVER MENU ITEMS --}}
        @if(auth()->user()->hasPermission('driver', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('driver/home') ? 'active' : '' }}" href="{{route('driver.home')}}">
                <i class="fas fa-truck"></i> Driver Dashboard
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('driver_orders', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('driver/my-orders') ? 'active' : '' }}" href="{{route('driver.my-orders')}}">
                <i class="fas fa-clipboard-list"></i> My Orders
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('chats', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('chats/*') ? 'active' : '' }}" href="{{route('chats.show')}}">
                <i class="fas fa-comments"></i> Chats
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('calls', 'view'))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('calls/*') ? 'active' : '' }}" href="{{route('calls.show')}}">
                <i class="fas fa-phone"></i> Calls
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a class="nav-link {{ request()->is('my-performance/*') ? 'active' : '' }}" href="{{route('my-performance.show')}}">
                <i class="fas fa-chart-line"></i> My Performance
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->is('my-wallet/*') ? 'active' : '' }}" href="{{route('my-wallet.show')}}">
                <i class="fas fa-wallet"></i> My Wallet
            </a>
        </li>
    </ul>
</div>

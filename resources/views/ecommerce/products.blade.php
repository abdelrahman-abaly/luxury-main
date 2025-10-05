@extends("layouts.main")

@section("content")
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<!-- Products Page -->
<div id="products">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Products</h2>
        <div class="btn-group" role="group">
            <!-- Export Button -->
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <!-- Import Button -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-upload me-1"></i> Import
            </button>
            <!-- Template Button -->
            <a href="{{ route('import-export.template', ['type' => 'products']) }}" class="btn btn-info">
                <i class="fas fa-file-download me-1"></i> Template
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="stripe row-border order-column" id="products-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Warehouse</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{$product->sku}}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                    $images = json_decode($product->images, true);
                                    $firstImage = !empty($images) && is_array($images) ? $images[0]['url'] ?? '' : '';
                                    @endphp
                                    @if($firstImage)
                                    <img src="{{$firstImage}}" alt="{{$images[0]['alt'] ?? 'Product'}}" class="product-img-thumb me-2">
                                    @else
                                    <div class="product-img-thumb me-2 bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 600;">{{$product->name}}</div>
                                        <div class="product-description">{{$product->description}}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>EGP {{$product->normal_price}}</div>
                                <small class="text-success">EGP {{$product->sale_price}} after sale</small>
                            </td>
                            <td>{{$product->category}}</td>
                            @if($product->status==="Published")
                            <td><span class="badge bg-success">Published</span></td>
                            @else
                            <td><span class="badge bg-info text-danger">Hidden</span></td>
                            @endif
                            <td>{{$product->warehouse}}</td>
                            @if($product->stock_quantity != "0")
                            <td><span class="badge bg-success">In Stock</span></td>
                            @else
                            <td><span class="badge bg-danger">Out Of Stock</span></td>
                            @endif

                            <td>
                                <a href="{{ route('orders.create', ['product_id' => $product->id]) }}"
                                    class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-cart-plus"></i> Add to Order
                                </a>
                                @if($product->stock_quantity === "0" || $product->stock_quantity === 0)
                                <button class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-bell"></i> Notify Me
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Warehouse</th>
                            <th>Stock</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section("scripts")
<script>
    $(document).ready(function() {
        let table = new DataTable('#products-table', {
            initComplete: function() {
                // Setup - add a text input to each footer cell
                $('#products-table tfoot th').each(function(i) {
                    var title = $('#products-table tfoot th')
                        .eq($(this).index())
                        .text();
                    $(this).html(
                        '<input type="text" placeholder="' + title + '" data-index="' + i + '" />'
                    );
                });

            },
            fixedHeader: {
                footer: true
            }
        });

        // Filter event handler
        $(table.table().container()).on('keyup', 'tfoot input', function() {
            table
                .column($(this).data('index'))
                .search(this.value)
                .draw();
        });
    });
</script>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.export') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="products">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_status" class="form-label">Status Filter</label>
                        <select class="form-select" id="export_status" name="status">
                            <option value="">All Statuses</option>
                            <option value="publish">Published</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_warehouse" class="form-label">Warehouse Filter</label>
                        <input type="text" class="form-control" id="export_warehouse" name="warehouse_id" placeholder="Warehouse ID">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="export_date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="export_date_from" name="date_from">
                        </div>
                        <div class="col-md-6">
                            <label for="export_date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="export_date_to" name="date_to">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="products">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="import_file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Supported formats: Excel (.xlsx, .xls), CSV (.csv). Max size: 10MB</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Make sure your file follows the template format. Download the template first if you're unsure.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

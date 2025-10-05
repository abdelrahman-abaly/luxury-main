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
<script>
    console.log("defaultProduct:", window.defaultProduct);
</script>

<!-- Add New Order Page -->
<div id="add-order">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add New Order</h2>
        <div class="d-flex">
            <form action="{{route('orders.list')}}" method="GET">
                @csrf
                <button type="submit" class="btn btn-outline-secondary me-2">Cancel</button>
            </form>
            <button onclick="saveOrder()" class="btn btn-primary">Save Changes</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="two-column-form">
                <div class="left-column">
                    <h5 class="mb-4">Customer Information</h5>

                    <div class="mb-3 d-block" id="select-customer">
                        <label class="form-label">Select Customer<span class="text-danger">*</span></label>
                        <select name="customer" class="form-select">
                            @foreach($customers as $customer)
                            <option value="{{$customer->lead_id}}">{{$customer->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="lead-info" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input name="full-name" type="text" class="form-control" placeholder="Enter customer full name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input name="phone" type="tel" class="form-control" placeholder="Enter customer phone number">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Governorate</label>
                        <select name="governorate" class="form-select">
                            <option selected disabled>Select governorate</option>
                            <option>Cairo</option>
                            <option>Alexandria</option>
                            <option>Giza</option>
                            <option>Aswan</option>
                            <option>Red Sea</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter full address"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any special notes"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <div class="input-group">
                            <input name="coupon-code" type="text" class="form-control" placeholder="Enter coupon code">
                            <button class="btn btn-outline-secondary">Apply</button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <input id="add-lead" type="checkbox" class="form-check-input" />
                        <span>Add As a Lead Instead</span>
                    </div>
                </div>

                <div class="right-column">
                    <h5 class="mb-4">Order Products</h5>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Current Order</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm" id="orderProducts">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Rows will be added dynamically -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <div class="text-end totals">
                                    <div class="mb-2">Subtotal: <strong>EGP 0</strong></div>
                                    <div class="mb-2">Shipping: <strong>EGP 50</strong></div>
                                    <div class="mb-2">Discount: <strong>-EGP 0</strong></div>
                                    <div class="h5">Total: <strong>EGP 50</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="product-search-container mb-1 d-flex justify-end">
                        <label class="form-label">Search Products:</label>
                    </div>

                    <div class="table-responsive">
                        <table class="stripe row-border order-column" id="product-list-table">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Warehouse</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr data-product-id="{{$product->id}}" data-price="{{$product->normal_price}}">
                                    <td id="product-{{$product->id}}-sku">{{$product->sku}}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{$product->images}}" alt="Product" class="product-img-thumb me-2">
                                            <div>
                                                <div id="product-{{$product->id}}-name" style="font-weight: 600;">{{$product->name}}</div>
                                                <div class="product-description">{{$product->description}}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>EGP {{$product->normal_price}}</div>
                                        <small class="text-success">EGP {{$product->sale_price}} after sale</small>
                                    </td>
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
                                        @if($product->stock_quantity != "0")
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="addProductToOrder(event, {{ $product->id }}, '{{ $product->name }}', {{ $product->normal_price }}, '{{ $product->images }}', '{{ $product->sku }}')">
                                            Add
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
    </div>
</div>

{{-- default product data --}}
<script>
    @if(isset($selectedProduct))
    window.defaultProduct = {
        id: {
            {
                $selectedProduct - > id
            }
        },
        name: "{{ $selectedProduct->name }}",
        price: {
            {
                $selectedProduct - > normal_price
            }
        },
        image: "{{ $selectedProduct->images }}",
        sku: "{{ $selectedProduct->sku }}",
        quantity: 1
    };
    @else
    window.defaultProduct = null;
    @endif
</script>
@endsection

@section("scripts")
{{-- Table Script--}}
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });



    $(document).ready(function() {
        let table = new DataTable('#product-list-table', {
            initComplete: function() {
                $('#product-list-table tfoot th').each(function(i) {
                    var title = $('#product-list-table tfoot th')
                        .eq($(this).index())
                        .text();
                    $(this).html('<input type="text" placeholder="' + title + '" data-index="' + i + '" />');
                });
            },
            fixedHeader: {
                footer: true
            }
        });

        $(table.table().container()).on('keyup', 'tfoot input', function() {
            table
                .column($(this).data('index'))
                .search(this.value)
                .draw();
        });
    });
</script>

{{-- ADD AS LEAD Logic --}}
<script>
    $(document).ready(function() {
        $('#add-lead').change(function() {
            if ($(this).is(':checked')) {
                $("#lead-info").removeClass("d-none").addClass("d-block");
                $("#select-customer").removeClass("d-block").addClass("d-none");
            } else {
                $("#lead-info").removeClass("d-block").addClass("d-none");
                $("#select-customer").removeClass("d-none").addClass("d-block");
            }
        });
    });
</script>

{{-- add to order - update quantities and total calc --}}
<script>
    let subtotal = 0;
    const shippingCost = 50;
    let discount = 0;
    let orderProducts = [];

    function addProductToOrder(event, productId, productName, productPrice, productImage, productSKU) {
        if (orderProducts.some(p => p.id === productId)) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "This product is already in your order!",
            });
            return;
        }

        orderProducts.push({
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            sku: productSKU,
            quantity: 1
        });

        updateOrderTable();

        $(event.target).addClass('btn-success').text('Added!');
        setTimeout(() => {
            $(event.target).removeClass('btn-success').text('Add');
        }, 1000);
    }

    function updateOrderTable() {
        const tbody = $('#orderProducts tbody');
        tbody.empty();
        let subtotal = 0;

        orderProducts.forEach(product => {
            const rowTotal = product.price * product.quantity;
            subtotal += rowTotal;

            const row = `
                    <tr data-product-id="${product.id}">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${product.image}" class="product-img-thumb me-2">
                                <div>
                                    <div>${product.name}</div>
                                    <small class="text-muted">${product.sku}</small>
                                </div>
                            </div>
                        </td>
                        <td>EGP ${product.price.toLocaleString()}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm quantity"
                                   value="${product.quantity}" min="1"
                                   onchange="updateQuantity(${product.id}, this.value)">
                        </td>
                        <td class="row-total">EGP ${rowTotal.toLocaleString()}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeProduct(${product.id})">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;

            tbody.append(row);
        });

        const shipping = 50;
        const grandTotal = subtotal + shipping;
        $('.totals .mb-2:eq(0) strong').text(`EGP ${subtotal.toLocaleString()}`);
        $('.totals .h5 strong').text(`EGP ${grandTotal.toLocaleString()}`);
    }

    function updateQuantity(productId, newQuantity) {
        const product = orderProducts.find(p => p.id === productId);
        if (product) {
            product.quantity = parseInt(newQuantity) || 1;
            updateOrderTable();
        }
    }

    function removeProduct(productId) {
        orderProducts = orderProducts.filter(p => p.id !== productId);
        updateOrderTable();
    }

    // function saveOrder() {
    //     var addLead = false;
    //     var customerID = $('select[name="customer"]').val();
    //     if ($('#add-lead').is(':checked')) {
    //         addLead = true;
    //         customerID = -1;
    //     }
    //     const orderData = {
    //         add_lead: addLead,
    //         customer_id: customerID,
    //         address: $('textarea[name="address"]').val(),
    //         notes: $('textarea[name="notes"]').val(),
    //         name: $('input[name="full-name"]').val(),
    //         phone: $('input[name="phone"]').val(),
    //         governorate: $('select[name="governorate"]').val(),
    //         coupon_code: $('input[name="coupon-code"]').val(),
    //         products: orderProducts,
    //         subtotal: orderProducts.reduce((sum, p) => sum + (p.price * p.quantity), 0),
    //         shipping: 50,
    //         total: orderProducts.reduce((sum, p) => sum + (p.price * p.quantity), 0) + 50,
    //     };

    //     $.post('/orders/store-order', orderData, function(response) {
    //         Swal.fire({
    //             title: "Order saved successfully!",
    //             icon: "success"
    //         });
    //     });
    // }

    function saveOrder() {
    var addLead = $('#add-lead').is(':checked');

    const orderData = {
        add_lead: addLead,
        customer_id: addLead ? null : $('select[name="customer"]').val(),
        address: $('textarea[name="address"]').val(),
        notes: $('textarea[name="notes"]').val(),
        name: $('input[name="full-name"]').val(),
        phone: $('input[name="phone"]').val(),
        governorate: $('select[name="governorate"]').val(),
        coupon_code: $('input[name="coupon-code"]').val(),
        products: orderProducts,
        subtotal: orderProducts.reduce((sum, p) => sum + p.price * p.quantity, 0),
        shipping: 50,
        total:
            orderProducts.reduce((sum, p) => sum + p.price * p.quantity, 0) +
            50,
    };

    $.ajax({
        url: '/orders/store-order',
        type: 'POST',
        data: orderData,
        success: function (res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: res.message });
            } else {
                Swal.fire({ icon: 'error', title: 'فشل', text: res.message });
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: xhr.responseJSON?.message || 'Something went wrong',
            });
        },
    });
}




    // === إضافة المنتج الافتراضي مباشرة لو موجود ===
    $(document).ready(function() {
        if (window.defaultProduct) {
            orderProducts.push(window.defaultProduct);
            updateOrderTable();
        }
    });
</script>
@endsection
<script>
    console.log('selectedProduct from PHP', @json($selectedProduct));
</script>

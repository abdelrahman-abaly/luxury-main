<!-- @extends("layouts.main")

@section("content") -->
    <!-- Repair Order Page -->
    <!-- <div id="repair-order">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Repairing Order Request</h2>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary me-2">Cancel</button>
                <button class="btn btn-primary">Submit Request</button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-4">Product Information</h5>

                        <div class="mb-4">
                            <label class="form-label">Upload Product Images</label>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                <p>Drag & drop product images here or click to browse</p>
                                <small class="text-small">Supports JPG, PNG (Max 5 images)</small>
                                <input type="file" id="fileInput" accept="image/jpeg, image/png" multiple style="display: none;">
                            </div>
                            <div class="d-flex mt-2 flex-wrap" id="previewContainer"> -->
                                <!-- Preview thumbnails will be added here dynamically -->
                            <!-- </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Search for Product</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Enter customer phone number">
                                <button class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="clientNotInSystem">
                                <label class="form-check-label" for="clientNotInSystem">
                                    Client is not in our system
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Maintenance Note</label>
                            <textarea class="form-control" rows="4" placeholder="Describe the maintenance issue or needed repair"></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-4">Previous Repair Requests</h5>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Product</th>
                                    <th>Warranty</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>REP-00123</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" alt="Product" class="me-2">
                                            Rolex Submariner
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">In Warranty</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>EGP 1,200</td>
                                    <td>15 Apr 2023</td>
                                </tr>
                                <tr>
                                    <td>REP-00119</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" alt="Product" class="me-2">
                                            Omega Seamaster
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Out of Warranty</span></td>
                                    <td><span class="badge bg-warning">Pending Approval</span></td>
                                    <td>EGP 2,500</td>
                                    <td>5 Mar 2023</td>
                                </tr>
                                <tr>
                                    <td>REP-00105</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" alt="Product" class="me-2">
                                            Tag Heuer Carrera
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">In Warranty</span></td>
                                    <td><span class="badge bg-info">In Progress</span></td>
                                    <td>-</td>
                                    <td>20 Feb 2023</td>
                                </tr>
                                <tr>
                                    <td>REP-00098</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" alt="Product" class="me-2">
                                            Breitling Navitimer
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Out of Warranty</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>EGP 3,800</td>
                                    <td>10 Jan 2023</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');
            const maxFiles = 5;
            let filesArray = []; -->
<!--
            // Click event for upload area
            uploadArea.addEventListener('click', () => fileInput.click());

            // Handle file selection
            fileInput.addEventListener('change', function(e) {
                if (this.files.length) {
                    handleFiles(this.files);
                }
            });

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadArea.classList.add('active');
            }

            function unhighlight() {
                uploadArea.classList.remove('active');
            }

            uploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    handleFiles(files);
                }
            });

            // Handle the selected files
            function handleFiles(files) {
                // Check if adding these files would exceed max limit
                if (filesArray.length + files.length > maxFiles) {
                    alert(`You can only upload a maximum of ${maxFiles} images.`);
                    return;
                }

                // Convert FileList to array and filter for images only
                const newFiles = Array.from(files).filter(file =>
                    file.type === 'image/jpeg' || file.type === 'image/png'
                );

                if (newFiles.length === 0) {
                    alert('Please upload only JPG or PNG images.');
                    return;
                }

                // Add to files array
                filesArray = [...filesArray, ...newFiles];

                // Update preview
                updatePreview();

                // Reset file input to allow selecting the same file again if needed
                fileInput.value = '';
            }

            // Update the preview thumbnails
            function updatePreview() {
                previewContainer.innerHTML = '';

                filesArray.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'preview-thumbnail';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';

                        const deleteBtn = document.createElement('button');
                        deleteBtn.className = 'delete-btn';
                        deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                        deleteBtn.onclick = () => removeFile(index);

                        previewDiv.appendChild(img);
                        previewDiv.appendChild(deleteBtn);
                        previewContainer.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Remove a file from the array
            function removeFile(index) {
                filesArray.splice(index, 1);
                updatePreview();
            }

            // You would call this when submitting the form
            window.getUploadedFiles = function() {
                return filesArray;
            };
        });
    </script>
@endsection -->




@extends("layouts.main")

@section("content")
    <!-- Repair Order Page -->
    <div id="repair-order">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Repairing Order Request</h2>
        </div>


        <!-- ✅ Form لعمل طلب جديد -->
        <form action="{{ route('orders.repairing-orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-4">Product Information</h5>

                            <!-- صور المنتج -->
                            <div class="mb-4">
                                <label class="form-label">Upload Product Images</label>
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p>Drag & drop product images here or click to browse</p>
                                    <small class="text-small">Supports JPG, PNG (Max 5 images)</small>
                                    <input type="file" name="product_images[]" id="fileInput" accept="image/jpeg, image/png" multiple style="display: none;">
                                </div>
                                <div class="d-flex mt-2 flex-wrap" id="previewContainer">
                                    <!-- Preview thumbnails will be added here dynamically -->
                                </div>
                            </div>

                            <!-- اسم المنتج -->
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>

                            <!-- الضمان -->
                            <div class="mb-3">
                                <label class="form-label">Warranty</label>
                                <select name="warranty" class="form-control" required>
                                    <option value="in_warranty">In Warranty</option>
                                    <option value="out_warranty">Out of Warranty</option>
                                </select>
                            </div>

                            <!-- الملاحظة -->
                            <div class="mb-3">
                                <label class="form-label">Maintenance Note</label>
                                <textarea name="maintenance_note" class="form-control" rows="4" placeholder="Describe the maintenance issue or needed repair" required></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-outline-secondary me-2">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </div>

                        <!-- الطلبات السابقة -->
                        <div class="col-md-6">
                            <h5 class="mb-4">Previous Repair Requests</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Product</th>
                                        <th>Warranty</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td>{{ $order->request_number }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @foreach(json_decode($order->product_images, true) ?? [] as $img)
                                                        <img src="{{ asset('storage/' . $img) }}" alt="Product" width="40" class="me-2 rounded">
                                                    @endforeach
                                                    {{ $order->product_name }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($order->warranty === 'in_warranty')
                                                    <span class="badge bg-success">In Warranty</span>
                                                @else
                                                    <span class="badge bg-danger">Out of Warranty</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge
                                                    @if($order->status === 'pending') bg-warning
                                                    @elseif($order->status === 'in_progress') bg-info
                                                    @elseif($order->status === 'completed') bg-success
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No repair requests yet.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end col -->
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');
            const maxFiles = 5;
            let filesArray = [];

            uploadArea.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', function() {
                if (this.files.length) handleFiles(this.files);
            });

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => uploadArea.classList.add('active'));
            ['dragleave', 'drop'].forEach(eventName => uploadArea.classList.remove('active'));

            uploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) handleFiles(files);
            });

            function handleFiles(files) {
                if (filesArray.length + files.length > maxFiles) {
                    alert(`You can only upload a maximum of ${maxFiles} images.`);
                    return;
                }

                const newFiles = Array.from(files).filter(file =>
                    file.type === 'image/jpeg' || file.type === 'image/png'
                );

                if (newFiles.length === 0) {
                    alert('Please upload only JPG or PNG images.');
                    return;
                }

                filesArray = [...filesArray, ...newFiles];
                updatePreview();
                fileInput.value = '';
            }

            function updatePreview() {
                previewContainer.innerHTML = '';
                filesArray.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'preview-thumbnail';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';

                        const deleteBtn = document.createElement('button');
                        deleteBtn.className = 'delete-btn';
                        deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                        deleteBtn.onclick = () => removeFile(index);

                        previewDiv.appendChild(img);
                        previewDiv.appendChild(deleteBtn);
                        previewContainer.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                });
            }

            function removeFile(index) {
                filesArray.splice(index, 1);
                updatePreview();
            }

            window.getUploadedFiles = function() {
                return filesArray;
            };
        });
    </script>
@endsection

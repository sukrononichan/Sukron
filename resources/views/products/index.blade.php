<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <nav class="mb-4">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="/products">Produk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/categories">Kategori</a>
            </li>
        </ul>
    </nav>

    <div class="row mb-4">
        <div class="col-md-12">
            <div id="alert-container"></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Daftar katalog</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                Tambah Produk
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="productsTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Diubah</th>
                    <th>Tanggal Publikasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 50px;">
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $product->is_publish ? 'bg-success' : 'bg-warning' }}">
                            {{ $product->is_publish ? 'Dipublikasi' : 'Draft' }}
                        </span>
                    </td>
                    <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $product->published_at ? $product->published_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-info edit-product" 
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-description="{{ $product->description }}"
                                    data-price="{{ $product->price }}"
                                    data-category="{{ $product->category_id }}"
                                    data-publish="{{ $product->is_publish }}"
                                    data-image="{{ $product->image }}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-product" data-id="{{ $product->id }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="productForm" novalidate>
                    <input type="hidden" id="productId">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">Nama produk wajib diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback">Deskripsi produk wajib diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" id="price" name="price" required min="0">
                        <div class="invalid-feedback">Harga produk tidak valid</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Kategori produk wajib dipilih</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div id="imagePreview" class="mt-2 d-none">
                            <img src="" alt="Preview" style="max-width: 200px;" class="img-thumbnail">
                        </div>
                        <div class="invalid-feedback">Gambar produk wajib diisi</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_publish" name="is_publish">
                        <label class="form-check-label">Publikasikan</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveProduct">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#productsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    // Check for alert after page load
    if (localStorage.getItem('showAlert') === 'true') {
        const type = localStorage.getItem('alertType') || 'success';
        showAlert(type, localStorage.getItem('alertMessage'));
        localStorage.removeItem('showAlert');
        localStorage.removeItem('alertMessage');
        localStorage.removeItem('alertType');
    }

    // Setup AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Show Alert
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alert-container').html(alert);
    }

    // Form Validation
    function validateForm() {
        const form = document.getElementById('productForm');
        let isValid = true;

        // Reset previous validation
        form.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
        });

        // Validate required fields
        form.querySelectorAll('[required]').forEach(element => {
            if (element.type === 'file') {
                // For file input, check if we're in edit mode and already have an image
                const productId = $('#productId').val();
                const hasExistingImage = productId && element.dataset.hasImage === 'true';
                if (!element.files[0] && !hasExistingImage) {
                    element.classList.add('is-invalid');
                    isValid = false;
                }
            } else if (!element.value) {
                element.classList.add('is-invalid');
                isValid = false;
            }
        });

        // Validate price
        const price = document.getElementById('price');
        if (price.value < 0) {
            price.classList.add('is-invalid');
            price.nextElementSibling.textContent = 'Harga produk minimal 0';
            isValid = false;
        }

        return isValid;
    }

    // Save Product
    $('#saveProduct').click(function() {
        if (!validateForm()) {
            return;
        }

        const id = $('#productId').val();
        const formData = new FormData();
        formData.append('_method', id ? 'PUT' : 'POST'); // Tambahkan method override untuk PUT
        formData.append('name', $('#name').val());
        formData.append('description', $('#description').val());
        formData.append('price', $('#price').val());
        formData.append('category_id', $('#category_id').val());
        formData.append('is_publish', $('#is_publish').is(':checked') ? 1 : 0);

        const imageFile = $('#image')[0].files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: id ? `/products/${id}` : '/products',
            type: 'POST', // Selalu gunakan POST, method override akan menangani PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#productModal').modal('hide');
                Swal.fire({
                    title: response.title,
                    text: response.message,
                    icon: response.status,
                    showConfirmButton: true
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    
                    Object.keys(errors).forEach(key => {
                        errorMessage += errors[key][0] + '<br>';
                    });
                    
                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                } else {
                    console.error('Server Error:', xhr);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                }
            }
        });
    });

    // Delete Product
    $('.delete-product').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data produk akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/products/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: response.status
                        }).then(() => {
                            localStorage.setItem('showAlert', 'true');
                            localStorage.setItem('alertMessage', response.message);
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: response.status
                        });
                    }
                });
            }
        });
    });

    // Edit Product
    $('.edit-product').click(function() {
        const button = $(this);
        $('#productId').val(button.data('id'));
        $('#name').val(button.data('name'));
        $('#description').val(button.data('description'));
        $('#price').val(button.data('price'));
        $('#category_id').val(button.data('category'));
        $('#is_publish').prop('checked', button.data('publish'));
        
        // Set data-has-image attribute
        const hasImage = button.data('image') ? true : false;
        $('#image').attr('data-has-image', hasImage);
        $('#image').prop('required', !hasImage);

        if (hasImage) {
            $('#imagePreview').removeClass('d-none');
            $('#imagePreview img').attr('src', '/images/products/' + button.data('image'));
        } else {
            $('#imagePreview').addClass('d-none');
        }

        $('#productModal').modal('show');
    });

    // Reset form when modal is closed
    $('#productModal').on('hidden.bs.modal', function() {
        $('#productForm').trigger('reset');
        $('#productId').val('');
        $('#image').attr('data-has-image', 'false');
        $('#image').prop('required', true);
        $('#imagePreview').addClass('d-none');
        $('#productForm .is-invalid').removeClass('is-invalid');
    });

    // Image Preview
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
                $('#imagePreview').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').addClass('d-none');
            $('#imagePreview img').attr('src', '');
        }
    });

    // Live Validation
    $('#productForm input, #productForm textarea, #productForm select').on('input change', function() {
        if (this.hasAttribute('required') && !this.value) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }

        if (this.id === 'price' && this.value < 0) {
            this.classList.add('is-invalid');
            this.nextElementSibling.textContent = 'Harga produk minimal 0';
        }
    });
});
</script>

</body>
</html>
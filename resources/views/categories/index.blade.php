<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedia Store</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <nav class="mb-4">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="/products">Products Game</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/categories">categories</a>
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
            <h2>Daftar Game</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                Tambah Kategori
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="categoriesTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Diubah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $index => $category)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <button class="btn btn-sm btn-info edit-category" 
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-category" data-id="{{ $category->id }}">
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveCategory">Simpan</button>
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
    $('#categoriesTable').DataTable({
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
        // Menghapus setTimeout untuk membiarkan alert tetap tampil
    }

    // Save Category
    $('#saveCategory').click(function() {
        const id = $('#categoryId').val();
        const formData = {
            name: $('#name').val()
        };

        const url = id ? `/categories/${id}` : '/categories';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#categoryModal').modal('hide');
                Swal.fire({
                    title: response.title,
                    text: response.message,
                    icon: response.status,
                    showConfirmButton: true
                }).then(() => {
                    localStorage.setItem('showAlert', 'true');
                    localStorage.setItem('alertMessage', response.message);
                    location.reload();
                });
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = '';
                for (const key in errors) {
                    errorMessage += `${errors[key]}\n`;
                }
                localStorage.setItem('showAlert', 'true');
                localStorage.setItem('alertMessage', errorMessage);
                localStorage.setItem('alertType', 'error');
                location.reload();
            }
        });
    });

    // Delete Category
    $('.delete-category').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data kategori akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/categories/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: response.status,
                            timer: 1500,
                            showConfirmButton: false
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
                        }).then(() => {
                            localStorage.setItem('showAlert', 'true');
                            localStorage.setItem('alertMessage', response.message);
                            localStorage.setItem('alertType', 'error');
                            location.reload();
                        });
                    }
                });
            }
        });
    });

    // Edit Category
    $('.edit-category').click(function() {
        const data = $(this).data();
        $('#categoryId').val(data.id);
        $('#name').val(data.name);
        $('#categoryModal').modal('show');
    });

    // Reset Form on Modal Close
    $('#categoryModal').on('hidden.bs.modal', function() {
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
    });
});
</script>

</body>
</html>

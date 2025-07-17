<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedia store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .card {
            transition: all 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        .card-img-placeholder {
            height: 200px;
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .search-box {
            border-radius: 20px;
            padding-left: 40px;
            background: #f8f9fa;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        .category-filter {
            border-radius: 20px;
        }
        .price-badge {
            background: linear-gradient(45deg, #007bff, #6610f2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-shop me-2"></i>Pedia Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products"><i class="bi bi-box-seam me-1"></i>Games</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories"><i class="bi bi-tags me-1"></i>Kategori</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">Store buat Gamers</h1>
                <p class="text-muted">Kebutuhan top up game anda, lengkap disini</p>
            </div>
            <div class="col-md-4">
                <div class="position-relative mb-3">
                    <i class="bi bi-search search-icon text-muted"></i>
                    <input type="text" class="form-control search-box" id="searchInput" placeholder="Cari produk...">
                </div>
                <select class="form-select category-filter" id="categoryFilter">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-4" id="productContainer">
            @foreach($products as $product)
            <div class="col-md-4 product-card" 
                 data-name="{{ strtolower($product->name) }}" 
                 data-category="{{ $product->category_id }}">
                <div class="card h-100">
                    @if($product->image)
                        <img src="{{ asset('images/products/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-placeholder">
                            <i class="bi bi-box-seam fs-1 text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $product->name }}</h5>
                            <span class="badge bg-info">{{ $product->category->name }}</span>
                        </div>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="price-badge">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                                <i class="bi bi-eye me-1"></i>Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Detail Produk -->
            <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h4 class="modal-title fw-bold">{{ $product->name }}</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-info fs-6">{{ $product->category->name }}</span>
                                <span class="price-badge">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="mb-4">{{ $product->description }}</p>
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-check-circle me-2"></i>Status: {{ $product->is_publish ? 'Dipublikasi' : 'Draft' }}
                                </small>
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-calendar me-2"></i>Dibuat: {{ $product->created_at->format('d/m/Y H:i') }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-clock me-2"></i>Diperbarui: {{ $product->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const productCards = document.querySelectorAll('.product-card');

            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;

                productCards.forEach(card => {
                    const productName = card.dataset.name;
                    const productCategory = card.dataset.category;
                    const matchesSearch = productName.includes(searchTerm);
                    const matchesCategory = !selectedCategory || productCategory === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterProducts);
            categoryFilter.addEventListener('change', filterProducts);
        });
    </script>
</body>
</html>
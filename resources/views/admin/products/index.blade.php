@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Products Management</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @php
                                        $primaryImage = $product->images->where('is_primary', true)->first();
                                    @endphp
                                    @if($primaryImage)
                                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-thumbnail" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($product->short_description ?? $product->description, 40) }}</small>
                                </td>
                                <td>
                                    <code>{{ $product->sku ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->sale_price)
                                        <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span><br>
                                        <span class="text-danger fw-bold">${{ number_format($product->sale_price, 2) }}</span>
                                    @else
                                        <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $stockClass = 'bg-success';
                                        if ($product->stock_quantity <= 0) {
                                            $stockClass = 'bg-danger';
                                        } elseif ($product->min_stock_level && $product->stock_quantity <= $product->min_stock_level) {
                                            $stockClass = 'bg-warning';
                                        }
                                    @endphp
                                    <span class="badge {{ $stockClass }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($product->status === 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->featured)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No products found. Create your first product!</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                        Add Product
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
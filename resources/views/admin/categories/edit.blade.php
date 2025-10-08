@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Edit Category</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Category Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" 
                               name="slug" 
                               value="{{ old('slug', $category->slug) }}">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <!-- Parent Category -->
                    <div class="col-md-6 mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" 
                                id="parent_id" 
                                name="parent_id">
                            <option value="">-- Root Category --</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" 
                                    {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-6 mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" 
                               class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', $category->sort_order) }}" 
                               min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Current Image -->
                @if($category->image)
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img src="{{ asset('storage/' . $category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px;">
                        </div>
                    </div>
                @endif

                <!-- Category Image -->
                <div class="mb-3">
                    <label for="image" class="form-label">
                        {{ $category->image ? 'Change Image' : 'Upload Image' }}
                    </label>
                    <input type="file" 
                           class="form-control @error('image') is-invalid @enderror" 
                           id="image" 
                           name="image" 
                           accept="image/*"
                           onchange="previewImage(event)">
                    <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WEBP (Max: 2MB)</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-3" style="display: none;">
                        <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active Status
                        </label>
                    </div>
                    <small class="text-muted">Inactive categories won't be displayed on the website</small>
                </div>

                <!-- Category Stats -->
                <div class="alert alert-info">
                    <strong>Category Statistics:</strong>
                    <ul class="mb-0">
                        <li>Subcategories: {{ $category->children()->count() }}</li>
                        <li>Products: {{ $category->products()->count() }}</li>
                    </ul>
                </div>

                <!-- Submit Buttons -->
               <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Update Category
    </button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
</form> <!-- 👈 close update form here -->

<form action="{{ route('admin.categories.destroy', $category->id) }}" 
      method="POST" 
      class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this category?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger mt-3">
        <i class="fas fa-trash"></i> Delete Category
    </button>
</form>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
    const name = e.target.value;
    const slug = name
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    
    document.getElementById('slug').value = slug;
});

// Image preview function
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
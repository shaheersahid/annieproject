@extends('admin.layouts.master')
@section('page-title', 'Edit Category')

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Edit Category: {{ $category->name }}</h4>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Back to Categories</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="ajaxForm" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Category image -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <!-- Show existing image if have -->
                                         @if($category->image_url)
                                           <div>
                                               <img src="{{ $category->image_url }}" alt="{{ seo_alt($category->name . ' category image') }}" height="80px" width="120px" class="rounded-md mb-2">
                                           </div>
                                         @endif

                                        <label for="image" class="form-label">Category Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                            id="name" name="name" value="{{ old('name', $category->name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control"
                                            id="slug" name="slug" value="{{ old('slug', $category->slug) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="parent_id" class="form-label">Parent Category</label>
                                        <select class="form-select" id="parent_id" name="parent_id">
                                            <option value="">None (Top Level)</option>
                                            @foreach($parentCategories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control"
                                            id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label d-block">Status</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_active" value="0">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label d-block">Home Page</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="show_on_home" value="0">
                                            <input class="form-check-input" type="checkbox" id="show_on_home" name="show_on_home" value="1" {{ old('show_on_home', $category->show_on_home) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_on_home">Show on Home Page</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control"
                                            id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                                        <small class="text-muted">Shown on the category page. Also used for meta description if SEO Meta Description is empty.</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h5 class="mb-3">SEO (storefront meta tags)</h5>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="seo_title" class="form-label">SEO Title</label>
                                        <input type="text" class="form-control" id="seo_title" name="seo_title" maxlength="255"
                                            value="{{ old('seo_title', data_get($category->seo?->meta_fields, 'title')) }}"
                                            placeholder="{{ $category->name }} Deals | Smart Comfort Deals">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="seo_description" class="form-label">SEO Meta Description</label>
                                        <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="500"
                                            placeholder="Unique summary for Google (recommended ~150–160 characters)">{{ old('seo_description', data_get($category->seo?->meta_fields, 'description')) }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="seo_keywords" class="form-label">SEO Keywords</label>
                                        <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" maxlength="500"
                                            value="{{ old('seo_keywords', data_get($category->seo?->meta_fields, 'keywords')) }}"
                                            placeholder="{{ $category->name }}, comfort deals, Amazon, Temu">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save me-1"></i> Update Category
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('admin-scripts')
    <script src="{{ asset('admin/assets/js/custom/index.js') }}"></script>
@endpush
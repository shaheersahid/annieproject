@php
    $isEdit = isset($attribute);
    $selectedCategories = collect(old('category_ids', $isEdit ? $attribute->categories->pluck('id')->all() : []));
@endphp

@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="row">
    <div class="col-lg-8">
        <x-admin.card title="Attribute Details">
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $attribute->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <textarea class="form-control @error('value') is-invalid @enderror" id="value" name="value" rows="4">{{ old('value', $attribute->value ?? '') }}</textarea>
                <small class="text-muted">Use one option per line for dropdown, radio, checkbox, and color switch.</small>
                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="short_description" class="form-label">Short Description</label>
                <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3">{{ old('short_description', $attribute->short_description ?? '') }}</textarea>
                @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </x-admin.card>
    </div>

    <div class="col-lg-4">
        <x-admin.card title="Settings">
            <div class="mb-3">
                <label for="input_type" class="form-label">Option Type <span class="text-danger">*</span></label>
                <select class="form-select @error('input_type') is-invalid @enderror" id="input_type" name="input_type" required>
                    @foreach($inputTypes as $value => $label)
                        <option value="{{ $value }}" {{ old('input_type', $attribute->input_type ?? 'dropdown') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('input_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="category_ids" class="form-label">Categories</label>
                <select class="form-select @error('category_ids') is-invalid @enderror" id="category_ids" name="category_ids[]" multiple>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $selectedCategories->contains($category->id) ? 'selected' : '' }}>
                            {{ $category->parent ? $category->parent->name . ' > ' : '' }}{{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="icon" class="form-label">Icon</label>
                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $attribute->icon ?? '') }}" placeholder="fa fa-tag">
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $attribute->sort_order ?? 0) }}">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $attribute->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Attribute' : 'Create Attribute' }}</button>
            </div>
        </x-admin.card>
    </div>
</div>

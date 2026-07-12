@php
    $isEdit = isset($tag);
@endphp

@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="row justify-content-center">
    <div class="col-lg-7">
        <x-admin.card title="{{ $isEdit ? 'Edit Tag' : 'Create Tag' }}">
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tag->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $tag->slug ?? '') }}">
                <small class="text-muted">Auto-generated from name if left empty.</small>
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $tag->type ?? 'product') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="option" class="form-label">Option</label>
                    <select class="form-select @error('option') is-invalid @enderror" id="option" name="option">
                        <option value="">No Option</option>
                        @foreach($options as $value => $label)
                            <option value="{{ $value }}" {{ old('option', $tag->option ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('option') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $tag->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Tag' : 'Create Tag' }}</button>
            </div>
        </x-admin.card>
    </div>
</div>

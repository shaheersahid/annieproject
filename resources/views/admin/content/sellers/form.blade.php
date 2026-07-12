@php
    $isEdit = isset($seller);
@endphp

@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="row">
    <div class="col-lg-8">
        <x-admin.card title="Seller Details">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="store_name" class="form-label">Store Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $seller->store_name ?? '') }}" required>
                    @error('store_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $seller->username ?? '') }}" required>
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="owner_name" class="form-label">Owner Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name', $seller->owner_name ?? '') }}" required>
                    @error('owner_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $seller->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $seller->phone ?? '') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                        <option value="Pakistan" {{ old('country', $seller->country ?? 'Pakistan') === 'Pakistan' ? 'selected' : '' }}>Pakistan</option>
                    </select>
                    @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="zip_code" class="form-label">Zip Code</label>
                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" name="zip_code" value="{{ old('zip_code', $seller->zip_code ?? '') }}">
                    @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $seller->location ?? '') }}">
                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="short_description" class="form-label">Short Description</label>
                <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3">{{ old('short_description', $seller->short_description ?? '') }}</textarea>
                @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </x-admin.card>
    </div>

    <div class="col-lg-4">
        <x-admin.card title="Media & Status">
            <div class="mb-3">
                <label for="store_logo" class="form-label">Store Logo</label>
                <input type="file" class="form-control @error('store_logo') is-invalid @enderror" id="store_logo" name="store_logo" accept="image/*">
                @if($isEdit && $seller->store_logo)
                    <img src="{{ asset('storage/'.$seller->store_logo) }}" class="avatar-lg rounded mt-2" alt="{{ $seller->store_name }}">
                @endif
                @error('store_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="cover_photo" class="form-label">Cover Photo</label>
                <input type="file" class="form-control @error('cover_photo') is-invalid @enderror" id="cover_photo" name="cover_photo" accept="image/*">
                @if($isEdit && $seller->cover_photo)
                    <img src="{{ asset('storage/'.$seller->cover_photo) }}" class="img-fluid rounded mt-2" alt="{{ $seller->store_name }}">
                @endif
                @error('cover_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $seller->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Seller' : 'Create Seller' }}</button>
            </div>
        </x-admin.card>
    </div>
</div>

@php
    $post = $blog ?? null;
    $publishDate = old('publish_date', $post?->published_at?->format('Y-m-d'));
    $publishTime = old('publish_time', $post?->published_at?->format('H:i'));
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="blogForm">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-1">Post Content</h5>
                    <p class="text-muted small mb-0">Write useful, structured content for readers.</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="blog-title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" id="blog-title" name="title"
                            class="form-control form-control-lg @error('title') is-invalid @enderror"
                            value="{{ old('title', $post?->title) }}" maxlength="255"
                            placeholder="Enter a clear, useful post title" required autofocus>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="blog-slug" class="form-label fw-semibold">URL Slug</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">/blog/</span>
                            <input type="text" id="blog-slug" name="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $post?->slug) }}" maxlength="255"
                                placeholder="generated-from-title">
                        </div>
                        @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="blog-excerpt" class="form-label fw-semibold">Excerpt</label>
                            <span class="text-muted small" id="excerptCount">0 / 500</span>
                        </div>
                        <textarea id="blog-excerpt" name="excerpt" rows="3" maxlength="500"
                            class="form-control @error('excerpt') is-invalid @enderror"
                            placeholder="Short summary shown on blog listing">{{ old('excerpt', $post?->excerpt) }}</textarea>
                        @error('excerpt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="blog-content" class="form-label fw-semibold">Article <span class="text-danger">*</span></label>
                        <textarea id="blog-content" name="content"
                            class="form-control @error('content') is-invalid @enderror"
                            rows="18">{{ old('content', $post?->content) }}</textarea>
                        @error('content') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-1">Search Preview</h5>
                    <p class="text-muted small mb-0">Optional metadata for search engines and social previews.</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="meta-title" class="form-label fw-semibold">Meta Title</label>
                        <input type="text" id="meta-title" name="meta_title"
                            class="form-control @error('meta_title') is-invalid @enderror"
                            value="{{ old('meta_title', $post?->meta_title) }}" maxlength="255"
                            placeholder="Defaults to post title">
                        @error('meta_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="meta-description" class="form-label fw-semibold">Meta Description</label>
                        <textarea id="meta-description" name="meta_description" rows="3" maxlength="500"
                            class="form-control @error('meta_description') is-invalid @enderror"
                            placeholder="Describe this article in one or two sentences">{{ old('meta_description', $post?->meta_description) }}</textarea>
                        @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="blog-sidebar">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Publishing</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="blog-status" class="form-label fw-semibold">Status</label>
                            <select id="blog-status" name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="draft" @selected(old('status', $post?->status ?? 'draft') === 'draft')>Draft</option>
                                <option value="published" @selected(old('status', $post?->status) === 'published')>Published</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Publish Schedule</label>
                            <div class="row g-2">
                                <div class="col-sm-7">
                                    <label for="publish-date" class="visually-hidden">Publish date</label>
                                    <input type="date" id="publish-date" name="publish_date"
                                        class="form-control @error('publish_date') is-invalid @enderror"
                                        value="{{ $publishDate }}">
                                </div>
                                <div class="col-sm-5">
                                    <label for="publish-time" class="visually-hidden">Publish time</label>
                                    <input type="time" id="publish-time" name="publish_time" step="60"
                                        class="form-control @error('publish_time') is-invalid @enderror"
                                        value="{{ $publishTime }}">
                                </div>
                            </div>
                            @error('publish_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @error('publish_time') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <p class="text-muted small mt-2 mb-0">Leave both blank to publish immediately. Future time schedules post.</p>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.blog.index') }}" class="btn btn-light flex-grow-1">Cancel</a>
                            <button type="submit" class="btn btn-primary flex-grow-1" id="savePostButton">
                                <i class="fas fa-save me-1"></i> {{ $submitLabel }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Featured Image</h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="imagePreview" class="{{ $post?->featured_image ? '' : 'd-none' }} blog-image-preview mb-3">
                            <img id="previewImg"
                                src="{{ $post?->featured_image ? resolve_image_path($post->featured_image) : '' }}"
                                alt="Featured image preview">
                        </div>
                        <label for="featuredImageInput" class="form-label fw-semibold">Choose Image</label>
                        <input type="file" id="featuredImageInput" name="featured_image"
                            class="form-control @error('featured_image') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/webp">
                        @error('featured_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <p class="text-muted small mt-2 mb-0">JPG, PNG or WebP. Maximum 2 MB.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

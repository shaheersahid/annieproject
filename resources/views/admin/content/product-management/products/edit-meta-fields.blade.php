@php
    $meta = old('meta_fields', $product->seo?->meta_fields ?? []);
    $og = old('og_fields', $product->seo?->og_fields ?? []);
    $twitter = old('twitter_fields', $product->seo?->twitter_fields ?? []);
    $schemaValue = old('schema_fields', $product->seo?->schema_fields
        ? json_encode($product->seo->schema_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        : '');
    $robots = $meta['robots'] ?? 'index, follow';
@endphp

@extends('admin.layouts.master')

@section('page-title', 'Product SEO')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="SEO: {{ $product->name }}" :items="[['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'SEO']]" />

        <form action="{{ route('admin.products.seo.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    <x-admin.card title="Meta Tags">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">SEO Title</label>
                                <input type="text" class="form-control @error('meta_fields.title') is-invalid @enderror" name="meta_fields[title]" maxlength="255" value="{{ $meta['title'] ?? '' }}" placeholder="{{ $product->name }}">
                                @error('meta_fields.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea class="form-control @error('meta_fields.description') is-invalid @enderror" name="meta_fields[description]" rows="3" maxlength="500" placeholder="{{ $product->getSeoDescription() }}">{{ $meta['description'] ?? '' }}</textarea>
                                @error('meta_fields.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keywords</label>
                                <input type="text" class="form-control" name="meta_fields[keywords]" maxlength="500" value="{{ $meta['keywords'] ?? '' }}" placeholder="comma separated keywords">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Canonical URL</label>
                                <input type="text" class="form-control" name="meta_fields[canonical]" value="{{ $meta['canonical'] ?? '' }}" placeholder="{{ $product->getSeoUrl() }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Robots</label>
                                <select class="form-select" name="meta_fields[robots]">
                                    @foreach(['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow'] as $option)
                                        <option value="{{ $option }}" @selected($robots === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-admin.card>

                    <x-admin.card title="Open Graph" class="mt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control" name="og_fields[title]" value="{{ $og['title'] ?? '' }}" placeholder="{{ $product->getSeoTitle() }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Type</label>
                                <input type="text" class="form-control" name="og_fields[type]" value="{{ $og['type'] ?? '' }}" placeholder="product">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <input type="text" class="form-control" name="og_fields[description]" value="{{ $og['description'] ?? '' }}" placeholder="{{ $product->getSeoDescription() }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Image URL</label>
                                <input type="text" class="form-control" name="og_fields[image]" value="{{ $og['image'] ?? '' }}" placeholder="{{ $product->getSeoImage() }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">URL</label>
                                <input type="text" class="form-control" name="og_fields[url]" value="{{ $og['url'] ?? '' }}" placeholder="{{ $product->getSeoUrl() }}">
                            </div>
                        </div>
                    </x-admin.card>

                    <x-admin.card title="Twitter Card" class="mt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Card</label>
                                <select class="form-select" name="twitter_fields[card]">
                                    @foreach(['summary_large_image' => 'Summary large image', 'summary' => 'Summary'] as $value => $label)
                                        <option value="{{ $value }}" @selected(($twitter['card'] ?? 'summary_large_image') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Site</label>
                                <input type="text" class="form-control" name="twitter_fields[site]" value="{{ $twitter['site'] ?? '' }}" placeholder="@smartcomfortdeals">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control" name="twitter_fields[title]" value="{{ $twitter['title'] ?? '' }}" placeholder="{{ $product->getSeoTitle() }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <input type="text" class="form-control" name="twitter_fields[description]" value="{{ $twitter['description'] ?? '' }}" placeholder="{{ $product->getSeoDescription() }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Image URL</label>
                                <input type="text" class="form-control" name="twitter_fields[image]" value="{{ $twitter['image'] ?? '' }}" placeholder="{{ $product->getSeoImage() }}">
                            </div>
                        </div>
                    </x-admin.card>

                    <x-admin.card title="Schema Markup" class="mt-3">
                        <p class="text-muted small">Leave this empty to use the automatic Product + Breadcrumb schema. Paste custom JSON only if you want to override it.</p>
                        <textarea class="form-control font-monospace @error('schema_fields') is-invalid @enderror" name="schema_fields" rows="8" placeholder='{"@context":"https://schema.org","@type":"Product"}'>{{ $schemaValue }}</textarea>
                        @error('schema_fields') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </x-admin.card>
                </div>

                <div class="col-lg-4">
                    <x-admin.card title="Auto schema preview">
                        <p class="text-muted small">This is what the product page outputs when the custom schema box is empty.</p>
                        <pre class="bg-light p-3 rounded small mb-0" style="max-height: 420px; overflow: auto;">{{ json_encode($product->defaultProductSchema(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </x-admin.card>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-light">Back</a>
                        <button type="submit" class="btn btn-primary">Save SEO</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

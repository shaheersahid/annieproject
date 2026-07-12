@extends('admin.layouts.master')

@section('page-title', 'Edit Product Meta Fields')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 text-white"><i class="fa fa-plus-circle me-1"></i> Edit Product Meta Fields ({{ $product->name }})</h5>
                    </div>
                    <div class="card-body mt-4">
                        <form action="{{ route('admin.products.seo.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Meta Tags -->
                            <h4 class="mb-3 text-dark">Meta Tags</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Canonical</label>
                                    <input type="text" class="form-control" name="meta_fields[canonical]" value="{{ data_get($product->seo, 'meta_fields.canonical') }}" placeholder="Enter Meta Canonical">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Robots</label>
                                    <input type="text" class="form-control" name="meta_fields[robots]" value="{{ data_get($product->seo, 'meta_fields.robots') }}" placeholder="Enter Meta Robots">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Keywords</label>
                                    <input type="text" class="form-control" name="meta_fields[keywords]" value="{{ data_get($product->seo, 'meta_fields.keywords') }}" placeholder="Enter Meta Keywords">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea class="form-control" name="meta_fields[description]" rows="3" placeholder="Enter Meta Description">{{ data_get($product->seo, 'meta_fields.description') }}</textarea>
                                </div>
                            </div>

                            <!-- Open Graph Links -->
                            <h4 class="mb-3 text-dark">Open Graph Links</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Title</label>
                                    <input type="text" class="form-control" name="og_fields[title]" value="{{ data_get($product->seo, 'og_fields.title') }}" placeholder="Enter graph title">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Type</label>
                                    <input type="text" class="form-control" name="og_fields[type]" value="{{ data_get($product->seo, 'og_fields.type') }}" placeholder="Enter graph type">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Image</label>
                                    <input type="text" class="form-control" name="og_fields[image]" value="{{ data_get($product->seo, 'og_fields.image') }}" placeholder="Enter graph image">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Url</label>
                                    <input type="text" class="form-control" name="og_fields[url]" value="{{ data_get($product->seo, 'og_fields.url') }}" placeholder="Enter graph url">
                                </div>
                            </div>

                            <!-- Twitter Cards Links -->
                            <h4 class="mb-3 text-dark">Twitter Cards Links</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Card</label>
                                    <input type="text" class="form-control" name="twitter_fields[card]" value="{{ data_get($product->seo, 'twitter_fields.card') }}" placeholder="Enter card summary">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Site</label>
                                    <input type="text" class="form-control" name="twitter_fields[site]" value="{{ data_get($product->seo, 'twitter_fields.site') }}" placeholder="Enter card site">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Title</label>
                                    <input type="text" class="form-control" name="twitter_fields[title]" value="{{ data_get($product->seo, 'twitter_fields.title') }}" placeholder="Enter card title">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <input type="text" class="form-control" name="twitter_fields[description]" value="{{ data_get($product->seo, 'twitter_fields.description') }}" placeholder="Enter card description">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Image</label>
                                    <input type="text" class="form-control" name="twitter_fields[image]" value="{{ data_get($product->seo, 'twitter_fields.image') }}" placeholder="Enter card image">
                                </div>
                            </div>

                            <!-- Schemas -->
                            <h4 class="mb-3 text-dark">Schemas</h4>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <textarea class="form-control" name="schema_fields" rows="4" placeholder="Enter Schema">{{ data_get($product->seo, 'schema_fields') ? json_encode(data_get($product->seo, 'schema_fields')) : '' }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-end">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-light px-3 d-inline-flex align-items-center"><i class="fa fa-times me-1"></i> Cancel</a>
                                    <button type="submit" class="btn btn-success px-3 ms-2 d-inline-flex align-items-center"><i class="fa fa-save me-1"></i> Save</button>
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
@extends('admin.layouts.master')
@section('page-title', 'Create Product')

@push('admin-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .product-editor .card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f7;
        }

        .product-editor .card-title {
            font-size: 1rem;
        }

        .product-editor form>.form-step .card {
            border: 1px solid #e8edf5 !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: .35rem;
            min-height: 38px;
        }

        .step-shell {
            border: 1px solid #e8edf5;
            border-radius: .9rem;
            padding: 1rem;
            background: #f8fafc;
            margin-bottom: 1.25rem;
        }

        .step-indicator {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .85rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .95rem;
            border: 1px solid #e1e7f0;
            border-radius: .75rem;
            background: #fff;
            cursor: pointer;
            transition: .18s ease;
        }

        .step-item:hover {
            border-color: #b8c4d8;
            transform: translateY(-1px);
        }

        .step-number {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #eef2f7;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .step-label {
            font-weight: 700;
            color: #334155;
        }

        .step-meta {
            display: block;
            font-size: .78rem;
            color: #7b8794;
        }

        .step-item.active {
            border-color: rgba(64, 81, 137, .45);
            box-shadow: 0 10px 26px rgba(64, 81, 137, .10);
        }

        .step-item.active .step-number {
            background: #405189;
            color: #fff;
        }

        .step-item.completed .step-number {
            background: #0ab39c;
            color: #fff;
        }

        .step-section-intro {
            border: 1px solid #e4eaf3;
            border-left: 4px solid #405189;
            border-radius: .75rem;
            background: #fff;
            padding: 1rem 1.1rem;
            margin-bottom: 1rem;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        @media (max-width: 991.98px) {
            .step-indicator {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Add New Product" :items="[
                ['label' => 'Products', 'url' => route('admin.products.index')],
                ['label' => 'Add New Product'],
            ]" />

            <x-admin.card class="product-editor">
                @include('admin.content.product-management.products.form-components.step-indicator', [
                    'activeStep' => 1,
                ])

                <form action="{{ route('admin.products.store') }}" method="POST" id="product-form"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="is_draft" id="is_draft" value="{{ old('is_draft', 0) ? 1 : 0 }}">
                    <input type="hidden" name="current_step" id="current_step" value="{{ old('current_step', 1) }}">

                    <div class="form-step active" id="step-1">
                        @include('admin.content.product-management.products.form-components.step-intro', [
                            'title' => 'Start with the commercial identity of the product',
                            'text' => 'Set title, product type, description, and catalogue organization.',
                        ])
                        <div class="row">
                            <div class="col-lg-8">
                                @include('admin.content.product-management.products.form-components.basic-information-card')
                                @include('admin.content.product-management.products.form-components.affiliate-card')
                                @include('admin.content.product-management.products.form-components.description-card')
                            </div>
                            <div class="col-lg-4">
                                @include('admin.content.product-management.products.form-components.organization-card')
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step-2">
                        @include('admin.content.product-management.products.form-components.step-intro', [
                            'title' => 'Add product media',
                            'text' => 'Upload a main image, product photos, and an optional video.',
                        ])
                        @include('admin.content.product-management.products.form-components.media-card')
                    </div>

                    <div class="form-step" id="step-3">
                        @include('admin.content.product-management.products.form-components.step-intro', [
                            'title' => 'Finish publishing details',
                            'text' => 'Add final specifications and choose whether this product is published.',
                        ])
                        <div class="row">
                            <div class="col-lg-8">
                                @include(
                                    'admin.content.product-management.products.form-components.details-card',
                                    ['isEdit' => false]
                                )
                            </div>
                            <div class="col-lg-4">
                                @include(
                                    'admin.content.product-management.products.form-components.publish-options-card',
                                    ['isEdit' => false]
                                )
                            </div>
                        </div>
                    </div>

                    @include(
                        'admin.content.product-management.products.form-components.navigation-footer',
                        ['submitLabel' => 'Create Product']
                    )
                </form>
            </x-admin.card>
        </div>
    </div>

    <template id="spec-template">
        <div class="spec-row row mb-2">
            <div class="col-5">
                <input type="text" class="form-control" name="specifications[items][__INDEX__][key]"
                    placeholder="Specification name">
            </div>
            <div class="col-5">
                <input type="text" class="form-control" name="specifications[items][__INDEX__][value]"
                    placeholder="Value">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-outline-danger remove-spec">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('admin-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('admin/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/products.js') }}"></script>
    <script>
        $(function() {
            $('.select2, .select2-categories').select2({
                placeholder: 'Select Options',
                allowClear: true
            });

            function toggleSizeChart() {
                const usesGuide = ['frame', 'service'].includes($('#product_type').val());
                $('#size-chart-wrapper').toggle(usesGuide);
                if (!usesGuide) {
                    $('#size_chart_id').val('');
                }
            }
            $('#product_type').on('change', toggleSizeChart);
            toggleSizeChart();
            ProductForm.init({
                specIndex: 0,
                existingImageCount: 0,
                redirectUrl: "{{ route('admin.products.index') }}",
                maxImages: 9
            });
        });
    </script>
@endpush

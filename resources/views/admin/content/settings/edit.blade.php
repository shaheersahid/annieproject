@extends('admin.layouts.master')
@section('page-title', 'Settings')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Settings" :items="[['label' => 'Settings']]" />

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-4">
                    <x-admin.card title="Delivery Settings">
                        <div class="mb-3">
                            <label for="delivery_origin_city" class="form-label">Origin City</label>
                            <input type="text" class="form-control @error('delivery_origin_city') is-invalid @enderror" id="delivery_origin_city" name="delivery_origin_city" value="{{ old('delivery_origin_city', $settings['delivery_origin_city']) }}" required>
                            @error('delivery_origin_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="delivery_default_charge" class="form-label">Default Delivery Charge</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" step="1" min="0" class="form-control @error('delivery_default_charge') is-invalid @enderror" id="delivery_default_charge" name="delivery_default_charge" value="{{ old('delivery_default_charge', $settings['delivery_default_charge']) }}" required>
                                @error('delivery_default_charge') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="free_delivery_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="free_delivery_enabled" name="free_delivery_enabled" value="1" @checked(old('free_delivery_enabled', $settings['free_delivery_enabled']))>
                            <label class="form-check-label" for="free_delivery_enabled">Enable Free Delivery</label>
                        </div>

                        <div class="mb-3">
                            <label for="free_delivery_min_order_amount" class="form-label">Free Delivery Order Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" step="1" min="0" class="form-control @error('free_delivery_min_order_amount') is-invalid @enderror" id="free_delivery_min_order_amount" name="free_delivery_min_order_amount" value="{{ old('free_delivery_min_order_amount', $settings['free_delivery_min_order_amount']) }}" required>
                                @error('free_delivery_min_order_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="free_delivery_min_item_quantity" class="form-label">Free Delivery Item Quantity</label>
                            <input type="number" step="1" min="0" class="form-control @error('free_delivery_min_item_quantity') is-invalid @enderror" id="free_delivery_min_item_quantity" name="free_delivery_min_item_quantity" value="{{ old('free_delivery_min_item_quantity', $settings['free_delivery_min_item_quantity']) }}" required>
                            @error('free_delivery_min_item_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </x-admin.card>
                </div>

                <div class="col-lg-8">
                    <x-admin.card title="City-to-City Delivery Charges">
                        <x-slot name="headerActions">
                            <button type="button" class="btn btn-light btn-sm" id="add-delivery-charge-row">
                                <i class="fas fa-plus-circle me-1"></i> Add Row
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i> Save Settings
                            </button>
                        </x-slot>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="delivery-charges-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>From City</th>
                                        <th>To City</th>
                                        <th style="width: 180px;">Charge</th>
                                        <th style="width: 120px;">Active</th>
                                        <th style="width: 90px;">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deliveryCharges as $index => $charge)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="delivery_charges[{{ $index }}][id]" value="{{ $charge->id }}">
                                                <input type="text" class="form-control" name="delivery_charges[{{ $index }}][from_city]" value="{{ old("delivery_charges.$index.from_city", $charge->from_city) }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="delivery_charges[{{ $index }}][to_city]" value="{{ old("delivery_charges.$index.to_city", $charge->to_city) }}">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">PKR</span>
                                                    <input type="number" step="1" min="0" class="form-control" name="delivery_charges[{{ $index }}][charge]" value="{{ old("delivery_charges.$index.charge", $charge->charge) }}">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="hidden" name="delivery_charges[{{ $index }}][is_active]" value="0">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="delivery_charges[{{ $index }}][is_active]" value="1" @checked(old("delivery_charges.$index.is_active", $charge->is_active))>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="hidden" name="delivery_charges[{{ $index }}][delete]" value="0">
                                                <input class="form-check-input" type="checkbox" name="delivery_charges[{{ $index }}][delete]" value="1">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-admin.card>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
    $(function () {
        let deliveryChargeIndex = {{ $deliveryCharges->count() }};

        $('#add-delivery-charge-row').on('click', function () {
            const index = deliveryChargeIndex++;
            $('#delivery-charges-table tbody').append(`
                <tr>
                    <td><input type="text" class="form-control" name="delivery_charges[${index}][from_city]" value=""></td>
                    <td><input type="text" class="form-control" name="delivery_charges[${index}][to_city]" value=""></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">PKR</span>
                            <input type="number" step="1" min="0" class="form-control" name="delivery_charges[${index}][charge]" value="0">
                        </div>
                    </td>
                    <td>
                        <input type="hidden" name="delivery_charges[${index}][is_active]" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="delivery_charges[${index}][is_active]" value="1" checked>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-light remove-delivery-charge-row">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $(document).on('click', '.remove-delivery-charge-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush

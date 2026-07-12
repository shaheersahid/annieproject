@extends('admin.layouts.master')
@section('page-title', 'Sales Returns')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #salesReturnModal .modal-dialog {
            max-width: min(1140px, calc(100vw - 1rem));
        }

        #salesReturnModal .modal-content {
            max-height: calc(100vh - 2rem);
            display: flex;
            overflow: hidden;
        }

        #salesReturnModal form {
            min-height: 0;
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
        }

        #salesReturnModal .modal-body {
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sr-context-card,
        .sr-summary-card,
        .sr-customer-card {
            border: 1px solid #e9ecef;
            border-radius: 0.75rem;
            background: #fff;
        }

        .sr-summary-value {
            font-size: 1.15rem;
            font-weight: 700;
        }

        .sr-summary-total {
            font-size: 1.5rem;
            font-weight: 800;
            color: #dc3545;
        }

        .sr-line-input {
            min-width: 120px;
        }

        .sr-line-total {
            font-weight: 700;
            color: #212529;
        }

        .sr-muted-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            font-weight: 700;
        }
    </style>
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Sales Returns" :items="[['label' => 'Sales Returns']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Sales Returns {{ auth()->user()->default_store_id ? '('.auth()->user()->defaultStore->name.')' : '' }}">
                    <x-slot name="headerActions">
                        @if(!auth()->user()->default_store_id)
                        <div class="input-group" style="width: 250px;">
                            <label class="input-group-text">Store</label>
                            <select class="form-select" id="sales-return-store-filter">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" id="sales-return-store-filter" value="{{ auth()->user()->default_store_id }}">
                        @endif

                        @can('edit orders')
                        <button type="button" class="btn btn-primary btn-sm" id="open-sales-return-create-modal">
                            <i class="fas fa-plus-circle me-1"></i> Add Sales Return
                        </button>
                        @endcan
                    </x-slot>

                    <x-admin.table id="sales-returns-table" :headers="[
                        'ID',
                        'Order',
                        'Customer',
                        'Store',
                        'Type',
                        'Refund Amount',
                        'Payment Status',
                        'Processed By',
                        'Created At',
                        'Action'
                    ]" />
                </x-admin.card>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="salesReturnModal" tabindex="-1" aria-labelledby="salesReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="salesReturnModalLabel">Create Sales Return</h5>
                    <p class="text-muted mb-0 small" id="salesReturnModalSubtext">Return against an existing order using the original order values.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="salesReturnForm">
                @csrf
                <input type="hidden" id="sales_return_id">

                <div class="modal-body">
                    <div class="alert alert-warning d-none" id="sales-return-edit-warning">
                        <strong>Finalized return:</strong> inventory and pricing are already posted. Only return reason, refund reference, and admin notes may be edited.
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Store</label>
                            <select class="form-select" id="sales_return_store_id" name="store_id" {{ auth()->user()->default_store_id ? 'disabled' : '' }}>
                                <option value="">Select store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ auth()->user()->default_store_id == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5" id="order-lookup-wrapper">
                            <label class="form-label">Order / Receipt Reference</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="sales_return_order_number" name="order_number" placeholder="Order number, POS invoice, or receipt token">
                                <button type="button" class="btn btn-outline-primary" id="lookup-sales-return-order">
                                    <i class="fas fa-search me-1"></i> Find Order
                                </button>
                            </div>
                            <div class="form-text">Search by original order reference only. Standalone/manual returns are not allowed.</div>
                        </div>
                        <div class="col-md-3" id="refund-method-wrapper">
                            <label class="form-label">Refund Method</label>
                            <select class="form-select" id="sales_return_refund_method" name="refund_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="voucher">Voucher</option>
                                <option value="points">Points</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="gateway">Gateway</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3 d-none" id="sales-return-context-grid">
                        <div class="col-lg-8">
                            <div class="sr-context-card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                                    <div>
                                        <div class="sr-muted-label">Original Order</div>
                                        <h5 class="mb-1" id="sr-order-number">-</h5>
                                        <div class="text-muted small" id="sr-order-reference">-</div>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <span class="badge bg-dark" id="sr-fulfillment-badge">-</span>
                                        <span class="badge bg-secondary" id="sr-payment-status-badge">-</span>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="sr-muted-label">Order Date</div>
                                        <div class="fw-semibold" id="sr-order-date">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sr-muted-label">Payment Method</div>
                                        <div class="fw-semibold" id="sr-payment-method">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sr-muted-label">Store</div>
                                        <div class="fw-semibold" id="sr-store-name">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="sr-customer-card p-3 h-100">
                                <div class="sr-muted-label">Customer Information</div>
                                <h6 class="mb-2" id="sr-customer-name">-</h6>
                                <div class="small text-muted mb-1" id="sr-customer-email">-</div>
                                <div class="small text-muted" id="sr-customer-phone">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card border shadow-none mb-0">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Return Items</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="min-width: 230px;">Product</th>
                                                    <th class="text-end">Ordered</th>
                                                    <th class="text-end">Returned</th>
                                                    <th class="text-end">Remaining</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-end">VAT %</th>
                                                    <th style="width: 145px;">Return Qty</th>
                                                    <th class="text-end">Refund Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sales-return-items-body">
                                                <tr id="sales-return-empty-row">
                                                    <td colspan="8" class="text-center text-muted py-4">Find an order to load returnable items.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="sr-summary-card p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Refund Summary</h6>
                                    <span class="badge bg-danger-subtle text-danger" id="sr-refund-live-badge">Draft</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal Refund</span>
                                    <span class="sr-summary-value" id="sr-refund-subtotal">PKR 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">VAT Refund</span>
                                    <span class="sr-summary-value" id="sr-refund-vat">PKR 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Refund Method</span>
                                    <span class="fw-semibold" id="sr-refund-method-preview">Cash</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Refund Reference</span>
                                    <span class="fw-semibold text-break text-end ms-3" id="sr-refund-reference-preview">Pending</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Final Refund Total</span>
                                    <span class="sr-summary-total" id="sr-refund-total">PKR 0.00</span>
                                </div>
                            </div>

                            <div class="card border shadow-none mb-0">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Original Order Totals</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Order Subtotal</span>
                                        <span class="fw-semibold" id="sr-order-subtotal">PKR 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Order Tax</span>
                                        <span class="fw-semibold" id="sr-order-tax">PKR 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Order Discount</span>
                                        <span class="fw-semibold" id="sr-order-discount">PKR 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Shipping</span>
                                        <span class="fw-semibold" id="sr-order-shipping">PKR 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Grand Total</span>
                                        <span class="fw-bold" id="sr-order-total">PKR 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Return Reason</label>
                            <textarea class="form-control" id="sales_return_reason" name="return_reason" rows="3" placeholder="Explain why the items are being returned" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Notes</label>
                            <textarea class="form-control" id="sales_return_notes" name="notes" rows="3" placeholder="Internal notes for this return"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Refund Reference</label>
                            <input type="text" class="form-control" id="sales_return_reference" name="refund_reference" placeholder="Optional transaction/reference number">
                        </div>
                        <div class="col-md-6 d-none" id="sr-edit-method-readonly-wrapper">
                            <label class="form-label">Refund Method</label>
                            <div class="form-control bg-light d-flex align-items-center" id="sr-edit-refund-method-readonly">-</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @can('edit orders')
                    <button type="submit" class="btn btn-primary" id="saveSalesReturnBtn" data-original-text="Save Return">Save Return</button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        const defaultStoreId = "{{ auth()->user()->default_store_id }}";
        let salesReturnMode = 'create';
        let loadedOrder = null;
        let loadedReturn = null;

        const table = $('#sales-returns-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.sales-returns.index') }}",
                data: function(d) {
                    d.store_id = $('#sales-return-store-filter').val() || defaultStoreId;
                }
            },
            columns: [
                { data: 'id', name: 'pos_returns.id' },
                { data: 'order_number', name: 'order.order_number', orderable: false, searchable: false },
                { data: 'customer_name', name: 'order.user.name', orderable: false, searchable: false },
                { data: 'store_name', name: 'order.store.name', orderable: false, searchable: false },
                { data: 'fulfillment_badge', name: 'order.fulfillment_type', orderable: false, searchable: false },
                { data: 'refund_amount', name: 'pos_returns.refund_amount' },
                { data: 'payment_status_badge', name: 'order.payment_status', orderable: false, searchable: false },
                { data: 'processed_by_name', name: 'processedBy.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'pos_returns.created_at' },
                { data: 'action', orderable: false, searchable: false }
            ],
            order: [[8, 'desc']]
        });

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function formatMoney(value) {
            return 'PKR ' + Number(value || 0).toFixed(2);
        }

        function formatQty(value, mode) {
            const num = Number(value || 0);
            if (mode === 'integer') {
                return String(Math.round(num));
            }

            return num.toFixed(3).replace(/\.?0+$/, '');
        }

        function paymentBadge(status) {
            const normalized = (status || '').toLowerCase();
            const classMap = {
                paid: 'success',
                pending: 'warning',
                failed: 'danger',
                refunded: 'info',
            };

            return '<span class="badge bg-' + (classMap[normalized] || 'secondary') + '">' + escapeHtml(normalized ? normalized.charAt(0).toUpperCase() + normalized.slice(1) : 'Unknown') + '</span>';
        }

        function setOriginalTotals(order) {
            $('#sr-order-subtotal').text(formatMoney(order.order_subtotal));
            $('#sr-order-tax').text(formatMoney(order.order_tax));
            $('#sr-order-discount').text(formatMoney(order.order_discount));
            $('#sr-order-shipping').text(formatMoney(order.order_shipping));
            $('#sr-order-total').text(formatMoney(order.order_total));
        }

        function setContextDetails(order) {
            $('#sales-return-context-grid').removeClass('d-none');
            $('#sr-order-number').text(order.order_number || '-');
            $('#sr-order-reference').text(order.reference ? 'Reference: ' + order.reference : 'Reference unavailable');
            $('#sr-order-date').text(order.date || '-');
            $('#sr-payment-method').text(order.payment_method || '-');
            $('#sr-store-name').text(order.store_name || '-');
            $('#sr-customer-name').text(order.customer_name || 'Guest');
            $('#sr-customer-email').text(order.customer_email || 'No email available');
            $('#sr-customer-phone').text(order.customer_phone || 'No phone available');
            $('#sr-fulfillment-badge')
                .text(order.fulfillment_type || '-')
                .removeClass('bg-dark bg-primary')
                .addClass((order.fulfillment_type || '').toLowerCase() === 'pos' ? 'bg-dark' : 'bg-primary');
            $('#sr-payment-status-badge').replaceWith($(paymentBadge(order.payment_status)).attr('id', 'sr-payment-status-badge'));
            setOriginalTotals(order);
        }

        function updateRefundReferencePreview() {
            const value = $('#sales_return_reference').val().trim();
            $('#sr-refund-reference-preview').text(value || 'Pending');
        }

        function updateRefundMethodPreview() {
            const label = salesReturnMode === 'edit'
                ? $('#sr-edit-refund-method-readonly').text().trim()
                : $('#sales_return_refund_method option:selected').text();

            $('#sr-refund-method-preview').text(label || '-');
        }

        function recalculateDraftRefund() {
            if (salesReturnMode !== 'create' || !loadedOrder) {
                return;
            }

            let subtotalRefund = 0;
            let vatRefund = 0;
            let finalRefund = 0;

            $('#sales-return-items-body tr[data-order-item-id]').each(function() {
                const row = $(this);
                const qty = parseFloat(row.find('.sales-return-qty').val() || 0);
                const perUnitSubtotal = parseFloat(row.data('per-unit-subtotal') || 0);
                const perUnitVat = parseFloat(row.data('per-unit-vat') || 0);
                const perUnitTotal = parseFloat(row.data('per-unit-total') || 0);

                const lineSubtotal = qty > 0 ? perUnitSubtotal * qty : 0;
                const lineVat = qty > 0 ? perUnitVat * qty : 0;
                const lineTotal = qty > 0 ? perUnitTotal * qty : 0;

                row.find('.sr-line-refund-subtotal').text(formatMoney(lineSubtotal));

                subtotalRefund += lineSubtotal;
                vatRefund += lineVat;
                finalRefund += lineTotal;
            });

            $('#sr-refund-subtotal').text(formatMoney(subtotalRefund));
            $('#sr-refund-vat').text(formatMoney(vatRefund));
            $('#sr-refund-total').text(formatMoney(finalRefund));
            updateRefundMethodPreview();
            updateRefundReferencePreview();
        }

        function renderCreateItems(items) {
            const tbody = $('#sales-return-items-body').empty();

            if (!items.length) {
                tbody.html('<tr><td colspan="8" class="text-center text-muted py-4">No returnable items remain on this order.</td></tr>');
                recalculateDraftRefund();
                return;
            }

            items.forEach(function(item, index) {
                const orderedQty = Number(item.ordered_quantity || 0);
                const perUnitSubtotal = orderedQty > 0 ? Number(item.line_subtotal || 0) / orderedQty : 0;
                const perUnitVat = orderedQty > 0 ? Number(item.line_vat || 0) / orderedQty : 0;
                const perUnitTotal = orderedQty > 0 ? Number(item.line_total || 0) / orderedQty : 0;

                tbody.append(
                    '<tr data-order-item-id="' + item.order_item_id + '"'
                    + ' data-per-unit-subtotal="' + perUnitSubtotal + '"'
                    + ' data-per-unit-vat="' + perUnitVat + '"'
                    + ' data-per-unit-total="' + perUnitTotal + '">'
                    + '<td>'
                    + '<div class="fw-semibold">' + escapeHtml(item.product_name) + '</div>'
                    + (item.variant_name ? '<div><small class="text-muted">' + escapeHtml(item.variant_name) + '</small></div>' : '')
                    + '<input type="hidden" class="sr-order-item-id" value="' + item.order_item_id + '">'
                    + '</td>'
                    + '<td class="text-end">' + formatQty(item.ordered_quantity, item.quantity_mode) + '</td>'
                    + '<td class="text-end text-muted">' + formatQty(item.returned_quantity, item.quantity_mode) + '</td>'
                    + '<td class="text-end fw-semibold">' + formatQty(item.remaining_quantity, item.quantity_mode) + '</td>'
                    + '<td class="text-end">' + formatMoney(item.unit_price) + '</td>'
                    + '<td class="text-end">' + Number(item.vat_rate || 0).toFixed(2) + '%</td>'
                    + '<td>'
                    + '<input type="number"'
                    + ' class="form-control form-control-sm sales-return-qty sr-line-input"'
                    + ' min="0"'
                    + ' max="' + item.remaining_quantity + '"'
                    + ' step="' + item.quantity_step + '"'
                    + ' data-quantity-mode="' + item.quantity_mode + '"'
                    + ' placeholder="0">'
                    + '</td>'
                    + '<td class="text-end sr-line-total sr-line-refund-subtotal">' + formatMoney(0) + '</td>'
                    + '</tr>'
                );
            });

            recalculateDraftRefund();
        }

        function renderEditItems(items, salesReturn) {
            const tbody = $('#sales-return-items-body').empty();

            if (!items.length) {
                tbody.html('<tr><td colspan="8" class="text-center text-muted py-4">No items found for this return.</td></tr>');
                return;
            }

            items.forEach(function(item) {
                tbody.append(
                    '<tr>'
                    + '<td>'
                    + '<div class="fw-semibold">' + escapeHtml(item.product_name) + '</div>'
                    + (item.variant_name ? '<div><small class="text-muted">' + escapeHtml(item.variant_name) + '</small></div>' : '')
                    + '</td>'
                    + '<td class="text-end">-</td>'
                    + '<td class="text-end">-</td>'
                    + '<td class="text-end">-</td>'
                    + '<td class="text-end">' + formatMoney(item.unit_price) + '</td>'
                    + '<td class="text-end">' + Number(item.vat_rate || 0).toFixed(2) + '%</td>'
                    + '<td><input type="text" class="form-control form-control-sm bg-light" value="' + escapeHtml(item.quantity_returned) + '" readonly></td>'
                    + '<td class="text-end sr-line-total">' + formatMoney(item.subtotal) + '</td>'
                    + '</tr>'
                );
            });

            $('#sr-refund-subtotal').text(formatMoney(salesReturn.refund_subtotal || 0));
            $('#sr-refund-vat').text(formatMoney(salesReturn.refund_vat || 0));
            $('#sr-refund-total').text(formatMoney(salesReturn.refund_amount || 0));
            $('#sr-refund-reference-preview').text(salesReturn.refund_reference || 'Pending');
            $('#sr-refund-method-preview').text(salesReturn.payment_method || '-');
            $('#sr-refund-live-badge').text('Finalized').removeClass('bg-danger-subtle text-danger').addClass('bg-success-subtle text-success');
        }

        function resetSalesReturnModal() {
            salesReturnMode = 'create';
            loadedOrder = null;
            loadedReturn = null;
            $('#salesReturnForm')[0].reset();
            $('#sales_return_id').val('');
            $('#salesReturnModalLabel').text('Create Sales Return');
            $('#salesReturnModalSubtext').text('Return against an existing order using the original order values.');
            $('#saveSalesReturnBtn').text('Save Return').data('original-text', 'Save Return');
            $('#sales-return-edit-warning').addClass('d-none');
            $('#order-lookup-wrapper').show();
            $('#sales_return_order_number').prop('readonly', false);
            $('#sales_return_store_id').prop('disabled', !!defaultStoreId);
            if (defaultStoreId) {
                $('#sales_return_store_id').val(defaultStoreId);
            }
            $('#sales_return_reason').prop('readonly', false);
            $('#sales_return_notes').prop('readonly', false);
            $('#sales_return_reference').prop('readonly', false);
            $('#refund-method-wrapper').removeClass('d-none');
            $('#sales_return_refund_method').prop('disabled', false);
            $('#sr-edit-method-readonly-wrapper').addClass('d-none');
            $('#sales-return-context-grid').addClass('d-none');
            $('#sales-return-items-body').html('<tr id="sales-return-empty-row"><td colspan="8" class="text-center text-muted py-4">Find an order to load returnable items.</td></tr>');
            $('#sr-refund-live-badge').text('Draft').removeClass('bg-success-subtle text-success').addClass('bg-danger-subtle text-danger');
            $('#sr-refund-subtotal, #sr-refund-vat, #sr-refund-total, #sr-order-subtotal, #sr-order-tax, #sr-order-discount, #sr-order-shipping, #sr-order-total').text('PKR 0.00');
            $('#sr-refund-reference-preview').text('Pending');
            $('#sr-refund-method-preview').text($('#sales_return_refund_method option:selected').text() || 'Cash');
            $('#sr-order-number, #sr-order-reference, #sr-order-date, #sr-payment-method, #sr-store-name, #sr-customer-name').text('-');
            $('#sr-customer-email, #sr-customer-phone').text('-');
            $('#sr-fulfillment-badge').text('-').removeClass('bg-dark bg-primary');
            $('#sr-payment-status-badge').replaceWith('<span class="badge bg-secondary" id="sr-payment-status-badge">-</span>');
        }

        function getSelectedStoreId() {
            return $('#sales_return_store_id').val() || defaultStoreId;
        }

        function clearLoadedOrderState(preserveInputs = false) {
            loadedOrder = null;
            loadedReturn = null;
            $('#sales-return-context-grid').addClass('d-none');
            $('#sales-return-items-body').html('<tr id="sales-return-empty-row"><td colspan="8" class="text-center text-muted py-4">Find an order to load returnable items.</td></tr>');
            $('#sr-refund-subtotal, #sr-refund-vat, #sr-refund-total, #sr-order-subtotal, #sr-order-tax, #sr-order-discount, #sr-order-shipping, #sr-order-total').text('PKR 0.00');
            $('#sr-refund-reference-preview').text('Pending');
            $('#sr-refund-method-preview').text($('#sales_return_refund_method option:selected').text() || 'Cash');
            $('#sr-order-number, #sr-order-reference, #sr-order-date, #sr-payment-method, #sr-store-name, #sr-customer-name').text('-');
            $('#sr-customer-email, #sr-customer-phone').text('-');
            $('#sr-fulfillment-badge').text('-').removeClass('bg-dark bg-primary');
            $('#sr-payment-status-badge').replaceWith('<span class="badge bg-secondary" id="sr-payment-status-badge">-</span>');

            if (!preserveInputs) {
                $('#sales_return_order_number').val('');
                $('#sales_return_reason').val('');
                $('#sales_return_notes').val('');
                $('#sales_return_reference').val('');
            }
        }

        $('#sales-return-store-filter').on('change', function() {
            table.draw();
        });

        $('#sales_return_store_id').on('change', function() {
            if (salesReturnMode !== 'create') {
                return;
            }

            const orderNumber = $('#sales_return_order_number').val().trim();

            clearLoadedOrderState(true);
            updateRefundReferencePreview();
            updateRefundMethodPreview();

            if (!orderNumber) {
                return;
            }

            $.getJSON("{{ route('admin.sales-returns.lookup-order') }}", {
                order_number: orderNumber,
                store_id: getSelectedStoreId()
            }).done(function(response) {
                loadedOrder = response.order;
                setContextDetails(loadedOrder);
                renderCreateItems(loadedOrder.items || []);
            }).fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                const message = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Order not found for the selected store.');
                toastr.error(message);
            });
        });

        $('#sales_return_refund_method').on('change', updateRefundMethodPreview);
        $('#sales_return_reference').on('input', updateRefundReferencePreview);

        $('#open-sales-return-create-modal').on('click', function() {
            resetSalesReturnModal();
            $('#salesReturnModal').modal('show');
        });

        $('#lookup-sales-return-order').on('click', function() {
            const orderNumber = $('#sales_return_order_number').val().trim();
            const storeId = getSelectedStoreId();

            if (!storeId) {
                toastr.error('Please select a store.');
                return;
            }

            if (!orderNumber) {
                toastr.error('Please enter an order or receipt reference.');
                return;
            }

            $.getJSON("{{ route('admin.sales-returns.lookup-order') }}", {
                order_number: orderNumber,
                store_id: storeId
            }).done(function(response) {
                loadedOrder = response.order;
                setContextDetails(loadedOrder);
                renderCreateItems(loadedOrder.items || []);
            }).fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                const message = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Failed to find order.');
                toastr.error(message);
            });
        });

        $('#sales-return-items-body').on('input change', '.sales-return-qty', function() {
            const input = $(this);
            const max = parseFloat(input.attr('max') || 0);
            const mode = input.data('quantity-mode');
            let value = parseFloat(input.val() || 0);

            if (Number.isNaN(value) || value < 0) {
                value = 0;
            }

            if (value > max) {
                value = max;
            }

            if (mode === 'integer') {
                value = Math.round(value);
            } else {
                value = parseFloat(value.toFixed(3));
            }

            input.val(value > 0 ? value : '');
            recalculateDraftRefund();
        });

        $('#sales-returns-table').on('click', '.edit-sales-return-btn', function() {
            const id = $(this).data('id');
            resetSalesReturnModal();
            salesReturnMode = 'edit';
            $('#salesReturnModalLabel').text('Review Sales Return');
            $('#salesReturnModalSubtext').text('Inventory and pricing are finalized. Only metadata may be updated.');
            $('#saveSalesReturnBtn').text('Save Metadata').data('original-text', 'Save Metadata');
            $('#sales-return-edit-warning').removeClass('d-none');
            $('#order-lookup-wrapper').hide();
            $('#refund-method-wrapper').addClass('d-none');
            $('#sales_return_refund_method').prop('disabled', true);
            $('#sr-edit-method-readonly-wrapper').removeClass('d-none');

            $.getJSON("{{ route('admin.sales-returns.show', ['salesReturn' => '__ID__']) }}".replace('__ID__', id))
                .done(function(response) {
                    loadedReturn = response.sales_return;
                    $('#sales_return_id').val(loadedReturn.id);
                    $('#sales_return_order_number').val(loadedReturn.order_number).prop('readonly', true);
                    $('#sales_return_store_id').val(loadedReturn.store_id).prop('disabled', true);
                    $('#sales_return_reason').val(loadedReturn.return_reason);
                    $('#sales_return_notes').val(loadedReturn.notes || '');
                    $('#sales_return_reference').val(loadedReturn.refund_reference || '');
                    $('#sr-edit-refund-method-readonly').text(loadedReturn.refund_method_label || '-');
                    updateRefundReferencePreview();
                    setContextDetails(loadedReturn);
                    $('#sr-payment-method').text(loadedReturn.payment_method || '-');
                    renderEditItems(loadedReturn.items || [], loadedReturn);
                    $('#salesReturnModal').modal('show');
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to load sales return.');
                });
        });

        $('#sales-returns-table').on('click', '.delete-sales-return-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            const orderNumber = btn.data('order');
            const storeId = btn.data('store-id');

            Swal.fire({
                title: 'Delete this sales return?',
                html: 'Order <strong>' + escapeHtml(orderNumber) + '</strong><br>This will remove the finalized return and reverse the stock that was restored from it.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete return',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.sales-returns.destroy', ['salesReturn' => '__ID__']) }}".replace('__ID__', id),
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (storeId) {
                            localStorage.setItem('inventory_refresh_signal', JSON.stringify({
                                timestamp: Date.now(),
                                store_id: storeId,
                                message: 'Inventory refreshed after sales return deletion.'
                            }));
                        }

                        toastr.success(response.message);
                        table.draw(false);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        const message = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Failed to delete sales return.');
                        toastr.error(message);
                    }
                });
            });
        });

        $('#salesReturnForm').on('submit', function(e) {
            e.preventDefault();

            const btn = $('#saveSalesReturnBtn');
            const originalText = btn.data('original-text');
            const url = salesReturnMode === 'edit'
                ? "{{ route('admin.sales-returns.update', ['salesReturn' => '__ID__']) }}".replace('__ID__', $('#sales_return_id').val())
                : "{{ route('admin.sales-returns.store') }}";

            let payload;

            if (salesReturnMode === 'create' && !loadedOrder) {
                toastr.error('Please find an order first.');
                return;
            }

            if (salesReturnMode === 'create') {
                payload = {
                    _token: "{{ csrf_token() }}",
                    store_id: getSelectedStoreId(),
                    order_number: $('#sales_return_order_number').val().trim(),
                    return_reason: $('#sales_return_reason').val(),
                    refund_method: $('#sales_return_refund_method').val(),
                    refund_reference: $('#sales_return_reference').val(),
                    notes: $('#sales_return_notes').val(),
                    items: []
                };

                $('#sales-return-items-body tr[data-order-item-id]').each(function() {
                    const row = $(this);
                    const qty = parseFloat(row.find('.sales-return-qty').val() || 0);
                    const orderItemId = row.find('.sr-order-item-id').val();

                    if (qty > 0) {
                        payload.items.push({
                            order_item_id: orderItemId,
                            quantity_returned: qty
                        });
                    }
                });

                if (!payload.items.length) {
                    toastr.error('Please enter at least one return quantity.');
                    return;
                }
            } else {
                payload = {
                    _token: "{{ csrf_token() }}",
                    _method: 'PUT',
                    return_reason: $('#sales_return_reason').val(),
                    refund_reference: $('#sales_return_reference').val(),
                    notes: $('#sales_return_notes').val()
                };
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: url,
                method: 'POST',
                data: payload,
                success: function(response) {
                    if (salesReturnMode === 'create') {
                        localStorage.setItem('inventory_refresh_signal', JSON.stringify({
                            timestamp: Date.now(),
                            store_id: getSelectedStoreId(),
                            message: 'Inventory refreshed from sales return stock restore.'
                        }));
                    }

                    toastr.success(response.message);
                    $('#salesReturnModal').modal('hide');
                    table.draw(false);
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    const message = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Something went wrong.');
                    toastr.error(message);
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush

@extends('admin.layouts.master')
@section('page-title', 'Expenses')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Expenses" :items="[['label' => 'Expenses']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Expense Log">
                    <x-slot name="headerActions">
                        @can('create expenses')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordExpenseModal">
                                <i class="fas fa-plus-circle me-1"></i> Record Expense
                            </button>
                        @endcan
                    </x-slot>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="store-filter" class="form-label">Store</label>
                            <select id="store-filter" class="form-select">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ auth()->user()->default_store_id == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-admin.table id="expenses-table" :headers="[
                        'Store',
                        'Category',
                        'Amount',
                        'Payment Method',
                        'Notes',
                        'Recorded By',
                        'Created At',
                        'Action'
                    ]" />
                </x-admin.card>
            </div>
        </div>
    </div>
</div>

@can('create expenses')
<div class="modal fade" id="recordExpenseModal" tabindex="-1" aria-labelledby="recordExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordExpenseModalLabel">Record Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.expenses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="expense_form_mode" value="create">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_store_id" class="form-label">Store <span class="text-danger">*</span></label>
                            <select class="form-select @error('store_id') is-invalid @enderror" id="modal_store_id" name="store_id" required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('store_id', auth()->user()->default_store_id) == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="modal_category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" id="modal_amount" name="amount" value="{{ old('amount') }}" required>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="modal_payment_method" name="payment_method" required>
                                <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                            @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="modal_notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="modal_notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@can('edit expenses')
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editExpenseForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="expense_form_mode" value="edit">
                <input type="hidden" name="edit_expense_id" id="edit_expense_id" value="{{ old('edit_expense_id') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_store_id" class="form-label">Store <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_store_id" name="store_id" required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="edit_amount" name="amount" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        const table = $('#expenses-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.expenses.index') }}",
                data: function(d) {
                    d.store_id = $('#store-filter').val();
                }
            },
            columns: [
                { data: 'store_name', name: 'store_id', orderable: false, searchable: false },
                { data: 'category_name', name: 'category_id', orderable: false, searchable: false },
                { data: 'amount', name: 'expenses.amount' },
                { data: 'payment_method_label', name: 'expenses.payment_method' },
                { data: 'notes', name: 'expenses.notes', orderable: false },
                { data: 'creator_name', name: 'created_by', orderable: false, searchable: false },
                { data: 'created_at', name: 'expenses.created_at' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        $('#store-filter').on('change', function() {
            table.ajax.reload();
        });

        $(document).on('click', '.expense-edit-trigger', function() {
            const modalEl = document.getElementById('editExpenseModal');
            if (!modalEl) return;

            $('#editExpenseForm').attr('action', $(this).data('update-url'));
            $('#edit_expense_id').val($(this).data('id'));
            $('#edit_store_id').val($(this).data('store-id'));
            $('#edit_category_id').val($(this).data('category-id'));
            $('#edit_amount').val($(this).data('amount'));
            $('#edit_payment_method').val($(this).data('payment-method') || 'cash');
            $('#edit_notes').val($(this).data('notes') || '');

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });

        @if(request()->boolean('create') || (old('expense_form_mode') === 'create' && $errors->any()))
            const modalEl = document.getElementById('recordExpenseModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        @endif

        @if(request()->filled('edit') || (old('expense_form_mode') === 'edit' && old('edit_expense_id')))
            const editModalEl = document.getElementById('editExpenseModal');
            const trigger = $(`.expense-edit-trigger[data-id="{{ old('edit_expense_id', request('edit')) }}"]`).first();
            if (editModalEl && trigger.length) {
                $('#editExpenseForm').attr('action', trigger.data('update-url'));
                $('#edit_expense_id').val(trigger.data('id'));
                $('#edit_store_id').val(trigger.data('store-id'));
                $('#edit_category_id').val(trigger.data('category-id'));
                $('#edit_amount').val(trigger.data('amount'));
                $('#edit_payment_method').val(trigger.data('payment-method') || 'cash');
                $('#edit_notes').val(trigger.data('notes') || '');

                const modal = new bootstrap.Modal(editModalEl);
                modal.show();
            }
        @endif
    });
</script>
@endpush

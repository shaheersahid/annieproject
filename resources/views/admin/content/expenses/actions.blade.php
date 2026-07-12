<div class="d-flex gap-2">
    @can('edit expenses')
        <button
            type="button"
            class="btn btn-outline-primary btn-sm expense-edit-trigger"
            title="Edit"
            data-id="{{ $expense->id }}"
            data-store-id="{{ $expense->store_id }}"
            data-category-id="{{ $expense->category_id }}"
            data-amount="{{ $expense->amount }}"
            data-payment-method="{{ $expense->payment_method }}"
            data-notes="{{ $expense->notes }}"
            data-update-url="{{ route('admin.expenses.update', $expense->id) }}"
        >
            <i class="fas fa-edit"></i>
        </button>
    @endcan

    @can('delete expenses')
        <x-admin.action-button type="delete" :url="route('admin.expenses.destroy', $expense->id)" />
    @endcan
</div>

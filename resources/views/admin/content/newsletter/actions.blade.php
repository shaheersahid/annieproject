<form action="{{ route('admin.newsletter.destroy', $subscriber) }}" method="POST" class="del_confirm">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
        <i class="mdi mdi-delete"></i>
    </button>
</form>
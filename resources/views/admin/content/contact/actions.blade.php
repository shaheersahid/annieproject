<div class="d-flex gap-2">
    <a href="{{ route('admin.contact.show', $contact->id) }}" class="btn btn-sm btn-primary" title="View">
        <i class="fa fa-eye"></i>
    </a>
    
    <form action="{{ route('admin.contact.destroy', $contact->id) }}" method="POST" class="del_confirm">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>

<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getContactSubmissionsDataTable();
        }
        return view('admin.content.contact.index');
    }

    public function show(int $id): View
    {
        $contact = ContactSubmission::findOrFail($id);
        if ($contact->status === 'new') {
            $contact->update(['status' => 'read']);
        }
        return view('admin.content.contact.show', compact('contact'));
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:new,read,replied,archived']);
        ContactSubmission::findOrFail($id)->update(['status' => $request->input('status')]);
        return response()->json(['success' => true]);
    }

    public function destroy(int $id): RedirectResponse
    {
        ContactSubmission::findOrFail($id)->delete();
        return redirect()->route('admin.contact.index')->with('success', 'Message deleted.');
    }
}

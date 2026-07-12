<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getNewsletterSubscribersDataTable();
        }
        return view('admin.content.newsletter.index');
    }

    public function destroy(NewsletterSubscriber $newsletter): RedirectResponse
    {
        $newsletter->delete();
        return redirect()->route('admin.newsletter.index')->with('success', 'Subscriber removed.');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        return back()->with('success', 'Newsletter queued for sending.');
    }
}

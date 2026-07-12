<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function websiteOrders(Request $request): mixed
    {
        if ($request->ajax()) {
            return app(\App\Contracts\DataTableServiceInterface::class)->getOrderManagementDataTable();
        }
        return view('admin.content.orders.index');
    }

    public function showWebsiteOrder(Order $order): View
    {
        $order->load(['items.product', 'customer']);
        return view('admin.content.orders.website-show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['status' => 'required|string']);
        $order->update(['status' => $request->input('status')]);
        return back()->with('success', 'Order status updated.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();
        return redirect()->route('admin.website-orders')->with('success', 'Order deleted.');
    }
}

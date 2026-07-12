<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReturnRefundController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getReturnRefundsDataTable();
        }
        return view('admin.content.orders-management.returns');
    }
}

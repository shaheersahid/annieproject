<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function dashboard(): View
    {
        return view('content.account.dashboard');
    }

    public function orders(): View
    {
        return view('content.account.orders');
    }

    public function notifications(): View
    {
        return view('content.account.notifications');
    }

    public function profile(): View
    {
        return view('content.account.profile');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class SocialiteController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        return redirect()->route('login');
    }
}

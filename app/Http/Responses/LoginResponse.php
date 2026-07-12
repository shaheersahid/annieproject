<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasRole('customer')) {
            return redirect()->route('account.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }
}

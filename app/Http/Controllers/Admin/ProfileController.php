<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $twoFactorEnabled = (bool) $user->two_factor_enabled;
        return view('admin.content.profile.edit', compact('user', 'twoFactorEnabled'));
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }
}

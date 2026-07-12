<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.content.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings saved.');
    }
}

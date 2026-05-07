<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'logo'           => Setting::get('logo'),
            'background'     => Setting::get('background'),
            'invoice_logo'   => Setting::get('invoice_logo'),
            'invoice_footer' => Setting::get('invoice_footer'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'logo'           => 'nullable|image|max:2048',
            'background'     => 'nullable|image|max:5120',
            'invoice_logo'   => 'nullable|image|max:2048',
            'invoice_footer' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('logo')) {
            $this->deleteFile(Setting::get('logo'));
            $path = $this->storeFile($request->file('logo'), 'logo');
            Setting::set('logo', $path);
        }

        if ($request->boolean('remove_logo')) {
            $this->deleteFile(Setting::get('logo'));
            Setting::set('logo', null);
        }

        if ($request->hasFile('background')) {
            $this->deleteFile(Setting::get('background'));
            $path = $this->storeFile($request->file('background'), 'background');
            Setting::set('background', $path);
        }

        if ($request->boolean('remove_background')) {
            $this->deleteFile(Setting::get('background'));
            Setting::set('background', null);
        }

        if ($request->hasFile('invoice_logo')) {
            $this->deleteFile(Setting::get('invoice_logo'));
            $path = $this->storeFile($request->file('invoice_logo'), 'invoice_logo');
            Setting::set('invoice_logo', $path);
        }

        if ($request->boolean('remove_invoice_logo')) {
            $this->deleteFile(Setting::get('invoice_logo'));
            Setting::set('invoice_logo', null);
        }

        Setting::set('invoice_footer', $request->input('invoice_footer') ?: null);

        return back()->with('success', 'Instellingen opgeslagen.');
    }

    private function storeFile(\Illuminate\Http\UploadedFile $file, string $name): string
    {
        $ext      = $file->getClientOriginalExtension();
        $filename = $name . '.' . $ext;
        $file->move(public_path('uploads/settings'), $filename);

        return 'uploads/settings/' . $filename;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}

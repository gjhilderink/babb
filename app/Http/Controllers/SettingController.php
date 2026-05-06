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
            'logo'       => Setting::get('logo'),
            'background' => Setting::get('background'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'logo'       => 'nullable|image|max:2048',
            'background' => 'nullable|image|max:5120',
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

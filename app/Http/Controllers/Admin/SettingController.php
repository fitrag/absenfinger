<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = Setting::getAllSettings();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:100',
            'school_website' => 'nullable|string|max:100',
            'principal_name' => 'nullable|string|max:255',
            'principal_nip' => 'nullable|string|max:50',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'system_name' => 'nullable|string|max:100',
            'letterhead' => 'nullable|string|max:255',
            'letterhead_sub' => 'nullable|string|max:255',
        ]);

        // Update text settings
        $textSettings = [
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_website',
            'principal_name',
            'principal_nip',
            'system_name',
            'letterhead',
            'letterhead_sub',
        ];

        foreach ($textSettings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store new logo
            $path = $request->file('school_logo')->store('logos', 'public');
            Setting::set('school_logo', $path);
        }

        // Handle kop image upload
        if ($request->hasFile('kop_image')) {
            // Delete old kop image if exists
            $oldKop = Setting::get('kop_image');
            if ($oldKop && Storage::disk('public')->exists($oldKop)) {
                Storage::disk('public')->delete($oldKop);
            }

            // Store new kop image
            $path = $request->file('kop_image')->store('kop', 'public');
            Setting::set('kop_image', $path);
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Remove logo.
     */
    public function removeLogo()
    {
        $logo = Setting::get('school_logo');
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }

        Setting::set('school_logo', null);
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Logo berhasil dihapus.');
    }

    /**
     * Remove kop image.
     */
    public function removeKopImage()
    {
        $kop = Setting::get('kop_image');
        if ($kop && Storage::disk('public')->exists($kop)) {
            Storage::disk('public')->delete($kop);
        }

        Setting::set('kop_image', null);
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Gambar kop surat berhasil dihapus.');
    }
}

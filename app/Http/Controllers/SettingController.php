<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings edit form.
     */
    public function edit()
    {
        $setting = Setting::current();
        return view('settings.edit', compact('setting'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name'  => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:1000',
            'website_link'  => 'nullable|string|max:255',
            'invoice_terms' => 'nullable|string|max:5000',
            'company_logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'remove_logo'   => 'nullable|boolean',
        ]);

        $setting = Setting::current();

        // Handle logo removal
        if ($request->boolean('remove_logo')) {
            if ($setting->company_logo) {
                $oldPath = public_path('uploads/logos/' . $setting->company_logo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $setting->company_logo = null;
            }
        }

        // Handle new logo upload
        if ($request->hasFile('company_logo')) {
            // Remove old logo if exists
            if ($setting->company_logo) {
                $oldPath = public_path('uploads/logos/' . $setting->company_logo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('company_logo');
            $uploadDir = public_path('uploads/logos');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $setting->company_logo = $filename;
        }

        $setting->company_name  = $validated['company_name'] ?? null;
        $setting->phone         = $validated['phone'] ?? null;
        $setting->address       = $validated['address'] ?? null;
        $setting->website_link  = $validated['website_link'] ?? null;
        $setting->invoice_terms = $validated['invoice_terms'] ?? null;
        $setting->save();

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully!');
    }
}

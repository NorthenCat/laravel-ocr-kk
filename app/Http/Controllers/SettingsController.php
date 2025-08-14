<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'string|nullable'
        ]);

        try {
            $settings = $request->input('settings', []);
            
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('settings.index')->with('error', 'Failed to update settings.');
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = $this->getAllSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'site_description' => 'nullable|string|max:1000',
            'currency' => 'nullable|string|max:10',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            $this->updateOrCreateSetting($key, $value);
        }

        // Clear settings cache
        Cache::forget('settings');

        return back()->with('success', 'Settings updated successfully!');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return back()->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    private function updateOrCreateSetting($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value ?? '']
        );
    }

    private function getAllSettings()
    {
        return Cache::remember('settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }
}
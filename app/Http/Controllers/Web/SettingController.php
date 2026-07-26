<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function __construct(private SystemSettingsService $systemSettings) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $systemSettings = $this->systemSettings->all();

        return view('settings.index', compact('user', 'systemSettings'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($request->user()->id)],
            'birth_date' => 'nullable|date|before:today',
        ]);

        $request->user()->update($request->only('name', 'email', 'birth_date'));

        return back()->with('success', __('messages.update_success'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update(['password' => bcrypt($request->password)]);

        return back()->with('success', __('messages.password_changed'));
    }

    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|max:2048',
            'brand_name' => 'nullable|string|max:255',
            'require_email_verification' => 'nullable|boolean',
            'bypass_password_validation' => 'nullable|boolean',
        ]);

        $existingSettings = $this->systemSettings->all();
        $logoPath = $existingSettings['logo_path'] ?? null;

        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $this->systemSettings->update([
            'brand_name' => $request->brand_name,
            'logo_path' => $logoPath,
            'require_email_verification' => $request->has('require_email_verification'),
            'bypass_password_validation' => $request->has('bypass_password_validation'),
        ]);

        return back()->with('success', __('messages.save_success'));
    }
}

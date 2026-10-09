<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', [
            'groups' => Setting::query()->orderBy('sort_order')->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = Setting::all()->keyBy('key');

        $rules = $settings->mapWithKeys(fn (Setting $setting): array => ['settings.'.$setting->key => match ($setting->type) {
            'email' => ['nullable', 'email:rfc', 'max:200'],
            'url' => ['nullable', 'url:https', 'max:300'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9]{7,15}$/'],
            'textarea' => ['nullable', 'string', 'max:600'],
            default => ['nullable', 'string', 'max:200'],
        }])->all();

        $validated = $request->validate($rules, [
            'settings.*.url' => 'Use a full https:// address.',
            'settings.*.regex' => 'Use digits only, optionally starting with +.',
        ]);

        DB::transaction(function () use ($settings, $validated): void {
            foreach ($validated['settings'] ?? [] as $key => $value) {
                $settings[$key]?->update(['value' => $value]);
            }
        });

        return redirect()->route('admin.settings.edit')->with('status', 'Settings saved.');
    }
}

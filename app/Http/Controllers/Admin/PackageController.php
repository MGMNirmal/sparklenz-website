<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Support\ListText;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index(): View
    {
        return view('admin.packages.index', [
            'packages' => Package::query()->with('tiers')->orderBy('sort_order')->get(),
        ]);
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.edit', ['package' => $package->load('tiers')]);
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'type_label' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'billing_unit' => ['required', 'string', 'max:40'],
            'tier_column_label' => ['required', 'string', 'max:80'],
            'price_column_label' => ['required', 'string', 'max:80'],
            'alt_price_column_label' => ['nullable', 'string', 'max:80'],
            'additional_item_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'additional_item_unit' => ['nullable', 'string', 'max:60'],
            'includes_text' => ['nullable', 'string', 'max:2000'],
            'notes_text' => ['nullable', 'string', 'max:2000'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'tiers' => ['array', 'max:12'],
            'tiers.*.id' => ['nullable', 'integer'],
            'tiers.*.delete' => ['boolean'],
            'tiers.*.label' => ['nullable', 'string', 'max:120'],
            'tiers.*.inclusions' => ['nullable', 'string', 'max:250'],
            'tiers.*.price' => ['nullable', 'required_with:tiers.*.label', 'integer', 'min:0', 'max:100000000'],
            'tiers.*.previous_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'tiers.*.alt_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ]);

        DB::transaction(function () use ($package, $validated, $request): void {
            $package->update([
                'name' => $validated['name'],
                'tagline' => $validated['tagline'] ?? null,
                'type_label' => $validated['type_label'],
                'description' => $validated['description'] ?? null,
                'billing_unit' => $validated['billing_unit'],
                'tier_column_label' => $validated['tier_column_label'],
                'price_column_label' => $validated['price_column_label'],
                'alt_price_column_label' => $validated['alt_price_column_label'] ?? null,
                'additional_item_price' => $validated['additional_item_price'] ?? null,
                'additional_item_unit' => $validated['additional_item_unit'] ?? null,
                'includes' => ListText::textToItems($validated['includes_text'] ?? ''),
                'notes' => ListText::textToItems($validated['notes_text'] ?? ''),
                'is_featured' => $request->boolean('is_featured'),
                'is_published' => $request->boolean('is_published'),
            ]);

            $order = 0;
            foreach ($validated['tiers'] ?? [] as $row) {
                $existing = filled($row['id'] ?? null) ? $package->tiers()->find($row['id']) : null;

                if (! empty($row['delete'])) {
                    $existing?->delete();

                    continue;
                }

                if (blank($row['label'] ?? null)) {
                    continue;
                }

                $fields = [
                    'label' => $row['label'],
                    'inclusions' => $row['inclusions'] ?? null,
                    'price' => (int) $row['price'],
                    'previous_price' => $row['previous_price'] ?? null,
                    'alt_price' => $row['alt_price'] ?? null,
                    'sort_order' => ++$order,
                ];

                $existing ? $existing->update($fields) : $package->tiers()->create($fields);
            }
        });

        return redirect()->route('admin.packages.edit', $package)->with('status', 'Package saved.');
    }
}

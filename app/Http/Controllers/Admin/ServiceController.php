<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\ListText;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::query()->withCount('rates')->orderBy('sort_order')->get(),
        ]);
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', ['service' => $service->load('rates')]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'summary' => ['required', 'string', 'max:250'],
            'intro' => ['nullable', 'string', 'max:1000'],
            'price_prefix' => ['nullable', 'string', 'max:20'],
            'starting_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'starting_unit' => ['nullable', 'string', 'max:80'],
            'starting_label' => ['nullable', 'string', 'max:80'],
            'includes_text' => ['nullable', 'string', 'max:3000'],
            'notes_text' => ['nullable', 'string', 'max:3000'],
            'is_published' => ['boolean'],
            'rates' => ['array', 'max:60'],
            'rates.*.id' => ['nullable', 'integer'],
            'rates.*.delete' => ['boolean'],
            'rates.*.group_label' => ['nullable', 'string', 'max:120'],
            'rates.*.label' => ['nullable', 'string', 'max:160'],
            'rates.*.detail' => ['nullable', 'string', 'max:200'],
            'rates.*.price_prefix' => ['nullable', 'string', 'max:20'],
            'rates.*.price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'rates.*.previous_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'rates.*.unit' => ['nullable', 'string', 'max:60'],
        ]);

        DB::transaction(function () use ($service, $validated, $request): void {
            $service->update([
                'name' => $validated['name'],
                'summary' => $validated['summary'],
                'intro' => $validated['intro'] ?? null,
                'price_prefix' => $validated['price_prefix'] ?? null,
                'starting_price' => $validated['starting_price'] ?? null,
                'starting_unit' => $validated['starting_unit'] ?? null,
                'starting_label' => $validated['starting_label'] ?? null,
                'includes' => ListText::textToGroups($validated['includes_text'] ?? ''),
                'notes' => ListText::textToItems($validated['notes_text'] ?? ''),
                'is_published' => $request->boolean('is_published'),
            ]);

            $order = 0;
            foreach ($validated['rates'] ?? [] as $row) {
                $existing = filled($row['id'] ?? null) ? $service->rates()->find($row['id']) : null;

                if (! empty($row['delete'])) {
                    $existing?->delete();

                    continue;
                }

                if (blank($row['label'] ?? null)) {
                    continue;
                }

                $fields = [
                    'group_label' => $row['group_label'] ?? null,
                    'label' => $row['label'],
                    'detail' => $row['detail'] ?? null,
                    'price_prefix' => $row['price_prefix'] ?? null,
                    'price' => $row['price'] ?? null,
                    'previous_price' => $row['previous_price'] ?? null,
                    'unit' => $row['unit'] ?? null,
                    'sort_order' => ++$order,
                ];

                $existing ? $existing->update($fields) : $service->rates()->create($fields);
            }
        });

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service saved.');
    }
}

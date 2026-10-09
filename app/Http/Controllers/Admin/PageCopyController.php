<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageCopy;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageCopyController extends Controller
{
    /**
     * @var array<string, string>
     */
    public const PAGES = [
        'home' => 'Home',
        'about' => 'About & team',
        'process' => 'Creative process',
        'services' => 'Services & packages',
        'work' => 'Our work',
        'contact' => 'Contact',
        'global' => 'Footer & call to action',
    ];

    public function edit(string $page = 'home'): View
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);

        return view('admin.copy', [
            'pages' => self::PAGES,
            'page' => $page,
            'entries' => PageCopy::where('page', $page)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, string $page): RedirectResponse
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);

        $entries = PageCopy::where('page', $page)->get()->keyBy('key');

        $validated = $request->validate($entries->mapWithKeys(fn (PageCopy $entry): array => [
            'copy.'.$entry->key => ['nullable', 'string', 'max:'.$entry->max_length],
        ])->all());

        DB::transaction(function () use ($entries, $validated): void {
            foreach ($validated['copy'] ?? [] as $key => $content) {
                $entries[$key]?->update(['content' => $content]);
            }
        });

        return redirect()->route('admin.copy.edit', $page)->with('status', 'Copy saved.');
    }
}

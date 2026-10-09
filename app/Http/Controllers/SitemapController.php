<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect(['home', 'work', 'services', 'about', 'contact'])->map(fn (string $name): string => route($name));

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain');
    }
}

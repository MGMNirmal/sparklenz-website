<?php

namespace Tests\Unit;

use App\Support\YouTube;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class YouTubeTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string|null}>
     */
    public static function links(): array
    {
        return [
            'watch url' => ['https://www.youtube.com/watch?v=M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'watch url with extras' => ['https://youtube.com/watch?t=30&v=M7lc1UVf-VE&list=x', 'M7lc1UVf-VE'],
            'mobile host' => ['https://m.youtube.com/watch?v=M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'short link' => ['https://youtu.be/M7lc1UVf-VE?si=abc', 'M7lc1UVf-VE'],
            'shorts' => ['https://www.youtube.com/shorts/aqz-KE-bpKQ', 'aqz-KE-bpKQ'],
            'embed' => ['https://www.youtube.com/embed/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'no scheme' => ['youtu.be/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'bare id' => ['M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'other host' => ['https://evil.example/watch?v=M7lc1UVf-VE', null],
            'lookalike host' => ['https://youtube.com.evil.example/watch?v=M7lc1UVf-VE', null],
            'bad id' => ['https://www.youtube.com/watch?v=<script>', null],
            'too short' => ['https://youtu.be/abc', null],
            'channel page' => ['https://www.youtube.com/@sparklenz', null],
            'iframe html' => ['<iframe src="https://www.youtube.com/embed/M7lc1UVf-VE"></iframe>', null],
            'javascript' => ['javascript:alert(1)', null],
            'empty' => ['', null],
        ];
    }

    #[DataProvider('links')]
    public function test_it_extracts_only_valid_youtube_ids(string $input, ?string $expected): void
    {
        $this->assertSame($expected, YouTube::extractId($input));
    }
}

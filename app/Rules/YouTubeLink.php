<?php

namespace App\Rules;

use App\Support\YouTube;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YouTubeLink implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || YouTube::extractId($value) === null) {
            $fail('Paste a YouTube link (youtube.com/watch?v=…, youtu.be/… or youtube.com/shorts/…) or an 11-character video ID.');
        }
    }
}

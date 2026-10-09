<?php

namespace App\Enums;

enum MediaType: string
{
    case Video = 'video';
    case Image = 'image';
    case Design = 'design';

    public function label(): string
    {
        return match ($this) {
            self::Video => 'Film',
            self::Image => 'Photography / stills',
            self::Design => 'Design',
        };
    }
}

<?php

namespace App\Enums;

enum EnquiryStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Read => 'Read',
            self::Replied => 'Replied',
        };
    }
}

<?php

namespace App\Enums;

enum ServiceType: string
{
    case PACKAGE = 'package';
    case UNIT = 'unit';

    public function label(): string
    {
        return match ($this) {
            self::PACKAGE => 'Paket',
            self::UNIT => 'Satuan',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

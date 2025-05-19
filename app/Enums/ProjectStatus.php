<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Hold = 'hold';
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');

    }
    public function color(): string
    {
        return match($this) {
            self::Active => 'green',
            self::Inactive => 'red',
            self::Hold => 'yellow',
        };
    }
}

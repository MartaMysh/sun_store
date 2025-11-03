<?php

namespace App\Enums;

enum ProductCategory: string
{
    case BATTERY = 'battery';
    case PANEL = 'panel';
    case CONNECTOR = 'connector';

    public function label(): string
    {
        return match($this) {
            self::BATTERY => 'Battery',
            self::PANEL => 'Solar Panel',
            self::CONNECTOR => 'Connector'
        };
    }
}
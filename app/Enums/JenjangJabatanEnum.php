<?php

namespace App\Enums;

enum JenjangJabatanEnum: string
{
	case AHLI_PERTAMA = 'ahli pertama';
	case AHLI_MUDA = 'ahli muda';
	case AHLI_MADYA = 'ahli madya';
	case AHLI_UTAMA = 'ahli utama';

	// Label panjang untuk Dropdown/Form
    public function label(): string
    {
        return match ($this) {
            self::AHLI_PERTAMA => 'Ahli Pertama',
            self::AHLI_MUDA => 'Ahli Muda',
            self::AHLI_MADYA => 'Ahli Madya',
            self::AHLI_UTAMA => 'Ahli Utama',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}

<?php
namespace App\Enums;

enum TujuanUjianEnum: string
{
    CASE KENAIKAN_JENJANG = 'kenaikan jenjang';
    CASE PERPINDAHAN_JABATAN = 'perpindahan jabatan';

    // Label panjang untuk Dropdown/Form
    public function label(): string
    {
        return match ($this) {
            self::KENAIKAN_JENJANG => 'Ujian Kompetensi Kenaikan Jenjang Jabatan',
            self::PERPINDAHAN_JABATAN => 'Ujian Kompetensi Perpindahan Jabatan',
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
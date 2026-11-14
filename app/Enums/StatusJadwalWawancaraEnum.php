<?php

namespace App\Enums;

enum StatusJadwalWawancaraEnum: string
{
	case TERJADWAL = 'terjadwal';
	case JADWAL_ULANG = 'jadwal ulang';
	case SELESAI = 'selesai';
	case MENUNGGU_HASIL = 'menunggu hasil';

	public function label(): string
    {
        return match($this) {
            self::TERJADWAL       => 'terjadwal',
            self::JADWAL_ULANG    => 'terjadwal ulang',
            self::SELESAI         => 'selesai',
            self::MENUNGGU_HASIL  => 'menunggu hasil',
        };
    }
}

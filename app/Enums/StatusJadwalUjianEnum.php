<?php

namespace App\Enums;

enum StatusJadwalUjianEnum: string
{
	case TERJADWAL = 'terjadwal';
	case SEDANG_BERLANGSUNG = 'sedang berlangsung';
	case MENUNGGU_HASIL = 'menunggu hasil';
	case SELESAI = 'selesai';
}

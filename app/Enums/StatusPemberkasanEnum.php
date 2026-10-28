<?php

namespace App\Enums;

enum StatusPemberkasanEnum: string
{
	case VERIFYING = 'verifikasi';
	case APPROVED = 'approved';
	case REJECTED = 'rejected';
    case SUMBIT_PORTOFOLIO = 'submit portofolio';

	public function label(): string
    {
        return match($this) {
            self::VERIFYING          => 'menunggu verifikasi',
            self::APPROVED           => 'lolos verifikasi',
            self::REJECTED           => 'tidak lolos verifikasi',
            self::SUMBIT_PORTOFOLIO  => 'menunggu verifikasi',
        };
    }
}

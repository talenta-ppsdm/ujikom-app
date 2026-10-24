<?php

namespace App\Enums;

enum StatusPemberkasanEnum: string
{
	case VERIFYING = 'verifikasi';
	case APPROVED = 'approved';
	case REJECTED = 'rejected';

	public function label(): string
    {
        return match($this) {
            self::VERIFYING => 'menunggu verifikasi',
            self::APPROVED  => 'lolos verifikasi',
            self::REJECTED  => 'tidak lolos verifikasi',
        };
    }
}

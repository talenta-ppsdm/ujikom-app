<?php

namespace App\Enums;

enum StatusPemberkasanEnum: string
{
	case VERIFYING = 'verifikasi';
	case APPROVED = 'approved';
	case REJECTED = 'rejected';
}

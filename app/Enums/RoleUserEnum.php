<?php

namespace App\Enums;

enum RoleUserEnum: string
{
	case PESERTA = 'peserta';
	case PENGUJI = 'penguji';
	case ADMIN = 'admin';
}

<?php

namespace Database\Seeders;

use App\Enums\RoleUserEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'name'     => 'Admin PPSDM',
                'password' => Hash::make('admin123'),
                'email' => 'admin@pkp.go.id',
                'role'     => RoleUserEnum::ADMIN->value,
                'nip'      => '198501012010011001',
            ]
        );
    }
}

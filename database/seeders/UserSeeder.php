<?php

namespace Database\Seeders;

use App\Enums\RoleUserEnum;
use App\Models\Admin;
use App\Models\Penguji;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User as Admin
        $admin = User::updateOrCreate(
            [
                'nip'   => '198501012010011001'
            ],[
                'name'     => 'Admin PPSDM',
                'password' => Hash::make('admin123'),
                'email' => 'admin@pkp.go.id',
                'role'     => RoleUserEnum::ADMIN->value,
            ]
        );

        Admin::updateOrCreate(
            [
                'nip' => '200106092025062015', 
            ],
            [
                'nama'      => 'Admin PPSDM',
                'telepon'   => '081318523690',
                'unit'      => 'Pusat Pengembangan Sumber Daya Manusia',
                'instansi'  => 'Kementerian Perumahan dan Kawasan Permukiman',
                'email'     => 'admin@pkp.go.id',
                'user_id'   => $admin->id,
            ]
        );

        // User as peserta
        $peserta = User::updateOrCreate(
            [
                'nip'   => '200106092025062015'
            ],[
                'name'     => 'Puteri Auli',
                'password' => Hash::make('Peserta2026'),
                'email'    => 'puteri@pkp.go.id',
                'role'     => RoleUserEnum::PESERTA->value,
            ]
        );

        Peserta::updateOrCreate(
            [
                'nip' => '200106092025062015', 
            ],
            [
                'nama'      => 'Puteri Aulia',
                'golongan'  => 'IIIa',
                'jabatan'   => 'Analis Sumber Daya Manusia Ahli Pertama',
                'unit'      => 'Pusat Pengembangan Sumber Daya Manusia',
                'instansi'  => 'Kementerian Perumahan dan Kawasan Permukiman',
                'telepon'   => '081345768764',
                'email'     => 'puteri@pkp.go.id',
                'user_id'   => $peserta->id,
            ]
        );

        // User as penguji
        $penguji = User::updateOrCreate(
            [
                'nip'   => '198706092025063857'
            ],[
                'name'     => 'Andra Zulfi, S.T., M.PWK.',
                'password' => Hash::make('Penguji123'),
                'email' => 'andraZ@pkp.go.id',
                'role'     => RoleUserEnum::PENGUJI->value,
            ]
        );

        Penguji::updateOrCreate(
            [
                'nip' => '198706092025063857', 
            ],
            [
                'nama'              => 'Puteri Aulia',
                'golongan'          => 'IVa',
                'jabatan'           => 'Penata Kelola Perumahan Hali Madya',
                'unit'              => 'Direktorat Pembiayaan Lahan',
                'instansi'          => 'Kementerian Perumahan dan Kawasan Permukiman',
                'telepon'           => '081318764563',
                'email'             => 'andraZ@pkp.go.id',
                'bidang_keahlian'   => 'Pembiayaan perumahan',
                'user_id'           => $penguji->id,
            ]
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_view_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'nip' => '123456789',
            'role' => 'admin',
        ]);
        $admin = Admin::create([
            'nip' => '123456789',
            'nama' => 'Admin Test',
            'unit' => 'Unit Test',
            'instansi' => 'Instansi Test',
            'telepon' => '08123456789',
            'email' => 'admin@example.com',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->put(route('admin.profil.photo', $admin->id), [
                'foto' => UploadedFile::fake()->image('profil.jpg'),
            ])
            ->assertRedirect(route('admin.profil'))
            ->assertSessionHas('success', 'Foto profil berhasil diperbarui.');

        $admin->refresh();
        $this->assertNotNull($admin->foto);
        Storage::disk('public')->assertExists($admin->foto);

        $response = $this->actingAs($user)
            ->get(route('admin.profil'))
            ->assertOk()
            ->assertSee(asset('storage/' . $admin->foto), false)
            ->assertSee('Foto profil Admin Test');

        $this->assertSame(3, substr_count($response->getContent(), asset('storage/' . $admin->foto)));
    }

    public function test_admin_cannot_upload_photo_to_another_admin_profile(): void
    {
        Storage::fake('public');
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'nip' => '123456789',
            'role' => 'admin',
        ]);
        $otherUser = User::create([
            'name' => 'Other Admin',
            'email' => 'other@example.com',
            'password' => 'password',
            'nip' => '987654321',
            'role' => 'admin',
        ]);
        $otherAdmin = Admin::create([
            'nip' => '987654321',
            'nama' => 'Other Admin',
            'unit' => 'Unit Test',
            'instansi' => 'Instansi Test',
            'telepon' => '08123456789',
            'email' => 'other@example.com',
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->put(route('admin.profil.photo', $otherAdmin->id), [
                'foto' => UploadedFile::fake()->image('profil.jpg'),
            ])
            ->assertNotFound();

        $this->assertNull($otherAdmin->fresh()->foto);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}

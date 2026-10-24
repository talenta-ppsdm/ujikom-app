<?php

namespace Tests\Feature;

use App\Enums\RoleUserEnum;
use App\Models\Pemberkasan;
use App\Models\User;
use App\Repositories\PemberkasanRepository;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class VerifikasiPemberkasanDocumentTest extends TestCase
{
    public function test_admin_can_open_an_uploaded_document_inline(): void
    {
        Storage::fake('public');
        $path = 'peserta/pemberkasan/SK_Jabatan_123_123.pdf';
        $contents = "%PDF-1.4\n%%EOF";
        Storage::disk('public')->put($path, $contents);

        $pemberkasan = new Pemberkasan(['file_sk_jabatan' => $path]);
        $repository = Mockery::mock(PemberkasanRepository::class);
        $repository->shouldReceive('find')->once()->with(1)->andReturn($pemberkasan);
        $this->app->instance(PemberkasanRepository::class, $repository);

        $response = $this->actingAs(new User(['role' => RoleUserEnum::ADMIN->value]))
            ->get(route('verifikasi-pemberkasan.document', ['id' => 1, 'document' => 'sk-jabatan']));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertStreamedContent($contents);
    }

    public function test_participants_cannot_open_uploaded_documents(): void
    {
        $repository = Mockery::mock(PemberkasanRepository::class);
        $repository->shouldNotReceive('find');
        $this->app->instance(PemberkasanRepository::class, $repository);

        $this->actingAs(new User(['role' => RoleUserEnum::PESERTA->value]))
            ->get(route('verifikasi-pemberkasan.document', ['id' => 1, 'document' => 'sk-jabatan']))
            ->assertForbidden();
    }
}

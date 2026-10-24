<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
use App\Repositories\PemberkasanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Illuminate\Support\Facades\Auth;

class VerifikasiPemberkasanController extends Controller
{
    protected PemberkasanRepository $pemberkasanRepository;

    public function __construct(PemberkasanRepository $pemberkasanRepository)
    {
        $this->pemberkasanRepository = $pemberkasanRepository;
    }

    public function index()
    {
        $pemberkasan = $this->pemberkasanRepository->all();

        return view('admin.verifikasiPemberkasan', compact('pemberkasan'));
    }

    public function edit(int $id)
    {
        $pemberkasan = $this->pemberkasanRepository->find($id);
       
        return view('admin.editVerifikasiPemberkasan', compact('pemberkasan'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->pemberkasanRepository->update([
                'status' => $request->status,
            ], $id);

             return redirect()->back();

        } catch (\Throwable $th) {
            return redirect()->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan sistem saat memproses data. Silakan coba beberapa saat lagi.');
        }
    }

    public function document(int $id, string $document)
    {
        abort_unless(Auth::user()->role === RoleUserEnum::ADMIN->value, 403);

        $field = match ($document) {
            'sk-golongan' => 'file_sk_golongan',
            'sk-jabatan' => 'file_sk_jabatan',
            'portofolio' => 'file_portofolio',
            default => abort(404),
        };

        $pemberkasan = $this->pemberkasanRepository->find($id);
        $path = $pemberkasan->{$field};

        abort_unless(
            is_string($path)
                && Str::startsWith($path, 'peserta/pemberkasan/')
                && !in_array('..', explode('/', $path), true),
            404
        );

        $disk = Storage::disk('public');
        $fullPath = Storage::disk('public')->path($path);
        abort_unless($disk->exists($path), 404);

        $mimeType = mime_content_type($fullPath);
        abort_unless(in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png'], true), 415);

        $stream = $disk->readStream($path);
        abort_if($stream === false, 404);

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => HeaderUtils::makeDisposition('inline', basename($path)),
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

}

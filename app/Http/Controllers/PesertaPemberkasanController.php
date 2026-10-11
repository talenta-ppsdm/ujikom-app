<?php

namespace App\Http\Controllers;

use App\Repositories\PemberkasanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaPemberkasanController extends Controller
{
    protected PemberkasanRepository $pemberkasanRepository;
    public function __construct(PemberkasanRepository $pemberkasanRepository)
    {
        $this->pemberkasanRepository = $pemberkasanRepository;
    }

    public function index()
    {
        $peserta = Auth::user()->peserta;
        $requiredProfileFields = [
            'nama',
            'nip',
            'golongan',
            'jabatan',
            'unit',
            'instansi',
            'telepon',
            'email',
        ];
        $isProfileIncomplete = !$peserta;

        if ($peserta) {
            foreach ($requiredProfileFields as $field) {
                if (blank($peserta->{$field})) {
                    $isProfileIncomplete = true;
                    break;
                }
            }
        }

        return view('peserta.pemberkasan', compact('isProfileIncomplete'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validateData = $request->validate([
            'sk_golongan'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'sk_jabatan'    => 'required|file|mimes:pdf|max:1024',
        ],[
            'sk_golongan.required'  => 'SK Golongan wajib diunggah.',
            'sk_golongan.file'  => 'SK Golongan wajib berupa file.',
            'sk_golongan.mimes'  => 'SK Golongan harus berformat PDF, JPG, JPEG atau PNG.',
            'sk_golongan.max'  => 'Ukuran file maksimal 1MB.',

            'sk_jabatan.required'  => 'SK Jabatan wajib diunggah.',
            'sk_jabatan.file'  => 'SK Jabatan wajib berupa file.',
            'sk_jabatan.mimes'  => 'SK Jabatan harus berformat PDF, JPG, JPEG atau PNG.',
            'sk_jabatan.max'  => 'Ukuran file maksimal 1MB.',
        ]);

        $this->pemberkasanRepository->create(array_merge($validateData,[
            'peserta_id'    => Auth::id(),
            'user_id'       => Auth::id()
        ]));
    }
}

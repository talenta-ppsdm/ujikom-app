<?php

namespace App\Http\Controllers;

use App\Exports\BankSoalTemplateExport;
use App\Exports\TemplateBankSoalExport;
use App\Imports\BankSoalImport;
use App\Repositories\BankSoalRepository;
use App\Models\BankSoal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BankSoalController extends Controller
{
    protected BankSoalRepository $bankSoalRepository;
    public function __construct(BankSoalRepository $bankSoalRepository)
    {
        $this->bankSoalRepository = $bankSoalRepository;
    }

    public function index()
    {
        $lastKode = $this->bankSoalRepository->getLastKode();
        if($lastKode !== null){
            $numberLastCode = $lastKode ? (int) substr($lastKode, 2) : 0;
        } else {
            $numberLastCode = 0;
        }
        $nextNumberCode = $numberLastCode + 1;
        $newCode = 'S-' . sprintf('%04d', $nextNumberCode);

        $bankSoal = $this->bankSoalRepository->all();

        $levels = BankSoal::getLevelLabels();
        $categories = BankSoal::getcategoryLabels();
        return view('banksoal', compact('newCode', 'bankSoal', 'levels', 'categories'));
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'kode' => 'required|string|max:50|unique:banksoal,kode',
                'kategori' => 'required|string|max:100',
                'level' => 'required|string|max:50',
                'poin' => 'required|integer|min:1',
                'soal' => 'required|string',
                'jawaban_a' => 'required|string',
                'jawaban_b' => 'required|string',
                'jawaban_c' => 'required|string',
                'jawaban_d' => 'required|string',
                'jawaban_e' => 'required|string',
                'kunci' => 'required|string|max:50',
                'pembahasan' => 'required|string',
            ]);
            
            $this->bankSoalRepository->create($validatedData);
    
            return redirect()->route('banksoal.index')->with('success', 'Data Bank Soal berhasil ditambahkan.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'fileBankSoal' => 'required|extensions:xlsx,xls',
        ]);
        $path = $request->file('fileBankSoal')->store('imports');
        Excel::import(new BankSoalImport($this->bankSoalRepository), $path);

        return redirect()->back()->with('success', 'Data soal berhasil di-import!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateBankSoalExport(), 'template-import-bank-soal.xlsx');
    }

    public function update(Request $request, int $id)
    {
        try{
            $validatedData = $request->validate([
                'kode' => 'required|string|max:50|unique:banksoal,kode,' . $id,
                'kategori' => 'required|string|max:100',
                'level' => 'required|string|max:50',
                'poin' => 'required|integer|min:1',
                'soal' => 'required|string',
                'jawaban_a' => 'required|string',
                'jawaban_b' => 'required|string',
                'jawaban_c' => 'required|string',
                'jawaban_d' => 'required|string',
                'jawaban_e' => 'required|string',
                'kunci' => 'required|string|max:50',
                'pembahasan' => 'required|string',
            ]);
    
            $this->bankSoalRepository->update($validatedData, $id);
    
            return redirect()->route('banksoal.index')->with('success', 'Data Bank Soal berhasil diperbarui.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(int $id)
    {
        try{
            $this->bankSoalRepository->delete($id);
            return redirect()->route('banksoal.index')->with('success', 'Data Bank Soal berhasil dihapus.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}

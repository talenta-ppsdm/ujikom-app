<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{BankSoal};
    
class BankSoalRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return BankSoal::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        // Add your boot logic here
    }

    public function getLastKode()
    {
        $lastBankSoal = $this->model->orderBy('kode', 'desc')->first();
        return $lastBankSoal ? $lastBankSoal->kode : null;
    }

    public function getCategoryByLevel(array $level)
    {
        $listKategori = $this->model->whereIn('level', $level)
            ->distinct()
            ->pluck('kategori');
        return $listKategori;
    }

    public function getByLevalAndCategory(array $level, string $kategori, $limit)
    {
        $soal = $this->model->whereIn('level', $level)
            ->where('kategori', $kategori)
            ->inRandomOrder()
            ->take($limit)
            ->get();
        return $soal;
    }

    public function getByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}
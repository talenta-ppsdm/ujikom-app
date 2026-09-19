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
        $lastKode = $this->model->where('kode', 'LIKE', 'S-%')
            ->orderByRaw('CAST(SUBSTRING(kode, 3) AS UNSIGNED) DESC')
            ->value('kode');

        return $lastKode ? $lastKode : null;;
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
}
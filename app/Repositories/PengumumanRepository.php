<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{Pengumuman};
    
class PengumumanRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Pengumuman::class;
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

    public function getPesertaActivePengumuman($pesertaId)
    {
        return $this->model
            ->where('status', 'aktif')
            ->where(function ($query) use ($pesertaId) {
                $query->where('tipe_target', 'semua')
                    ->orWhereHas('penerima', function ($subQuery) use ($pesertaId) {
                        $subQuery->where('peserta_id', $pesertaId);
                    });
            })
            ->orderByDesc('tgl_terbit')
            ->get();
    }
}
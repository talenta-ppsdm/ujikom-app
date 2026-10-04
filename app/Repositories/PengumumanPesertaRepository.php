<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{PengumumanPeserta};
    
class PengumumanPesertaRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return PengumumanPeserta::class;
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

    public function getByPengumumanAndPeserta(int $pengumumanId, int $pesertaId)
    {
        return $this->model->where([
            'pengumuman_id' => $pengumumanId,
            'peserta_id' => $pesertaId
        ])->first();
    }
}
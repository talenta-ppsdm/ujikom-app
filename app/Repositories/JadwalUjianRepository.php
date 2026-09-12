<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{JadwalUjian};
    
class JadwalUjianRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return JadwalUjian::class;
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

    public function getByPesertaAndStatus(int $pesertId, array $status)
    {
        $jadwal = $this->model->where('peserta_id', '=', $pesertId)
            ->whereIn('status', $status)
            ->first();
        return $jadwal;
    }
}
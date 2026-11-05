<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{JawabanUjian};
    
class JawabanUjianRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return JawabanUjian::class;
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

    public function getByPesertaAndJadwalCbt(int $pesertaId, int $jadwalCbtId)
    {
        return $this->model->where(
            [
                'peserta_id'=> $pesertaId,
                'cbt_id'    => $jadwalCbtId
            ]
        )->get();
    }

    public function getByPesertaAndJadwalUjian(int $pesertaId, int $jadwalUjianId)
    {
        return $this->model->where(
            [
                'peserta_id'        => $pesertaId,
                'jadwal_ujian_id'   => $jadwalUjianId
            ]
        )->get();
    }
}
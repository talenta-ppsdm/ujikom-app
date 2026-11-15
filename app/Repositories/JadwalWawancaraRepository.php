<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{JadwalWawancara};
    
class JadwalWawancaraRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return JadwalWawancara::class;
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

    public function getByPesertaJadwalAndStatus(int $idPeserta, int $idJadwalUjian, string $status)
    {
        return $this->model->where(
            [
                'peserta_id'        => $idPeserta,
                'jadwal_ujian_id'   => $idJadwalUjian,
                'status'            => $status
            ]
        );
    }

    public function getByPeserta(int $idPeserta)
    {
        return $this->model->where('peserta_id', $idPeserta);
    }
}
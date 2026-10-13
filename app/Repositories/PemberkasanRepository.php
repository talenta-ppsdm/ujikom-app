<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{Pemberkasan};
    
class PemberkasanRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Pemberkasan::class;
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

    public function getLastPemberkasan(int $idJadwalPemberkasan)
    {
        return $this->model->where('jadwal_pemberkasan_id', $idJadwalPemberkasan)
            ->orderBy('nomor_urut', 'desc')
            ->first();
    }

    public function getByPesertaAndScheduleWithStatus(int $idPeserta, int $idJadwalPemberkasan, string $status)
    {
        return $this->model->where([
            'peserta_id'            => $idPeserta,
            'jadwal_pemberkasan_id' => $idJadwalPemberkasan,
            'status'                => $status
        ])->first();
    }
}
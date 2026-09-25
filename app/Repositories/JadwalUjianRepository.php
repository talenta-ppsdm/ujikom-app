<?php
    
namespace App\Repositories;

use App\Enums\StatusJadwalUjianEnum;
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
            ->get();
        return $jadwal;
    }

    public function getByPeserta(int $pesertId)
    {
        return $this->model->where('peserta_id', "=", $pesertId)->get();
    }

    public function hasActiveUjian(int $pesertaId)
    {
        return $this->model->where('peserta_id', '=', $pesertaId)
            ->whereIn('status', [
                StatusJadwalUjianEnum::SEDANG_BERLANGSUNG->value,
                StatusJadwalUjianEnum::TERJADWAL->value
            ])->exists(); //Returns boolean true if it exists, false if it doesn’t
    }
}
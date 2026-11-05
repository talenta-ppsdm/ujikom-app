<?php
    
namespace App\Repositories;

use App\Enums\StatusJadwalCbtEnum;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{Cbt, JadwalUjian};
    
class CbtRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Cbt::class;
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

    public function getByPesertaAndYear(int $pesertId, int $year)
    {
        return $this->model->where('peserta_id', "=", $pesertId)
            ->whereYear('tanggal_ujian', $year)
            ->get();
    }

    public function getAvailableYearsByPeserta(int $pesertaId)
    {
        return $this->model->where('peserta_id', $pesertaId)
            ->whereNotNull('tanggal_ujian')
            ->selectRaw('DISTINCT strftime("%Y", tanggal_ujian) as year')
            ->pluck('year')
            ->filter()
            ->sortDesc(); // Dari tahun terbaru ke lama
    }

    public function hasActiveUjian(int $pesertaId)
    {
        return $this->model->where('peserta_id', '=', $pesertaId)
            ->whereIn('status', [
                StatusJadwalCbtEnum::SEDANG_BERLANGSUNG->value,
                StatusJadwalCbtEnum::TERJADWAL->value
            ])->exists(); //Returns boolean true if it exists, false if it doesn’t
    }

        public function getByPesertaAndJadwalUjian(int $pesertaId, int $jadwalUjianId)
        {
            return $this->model->where([
                'peserta_id' => $pesertaId,
                'jadwal_ujian_id' => $jadwalUjianId
            ])->first();
        }
}
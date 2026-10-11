<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{JadwalPemberkasan};
    
class JadwalPemberkasanRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return JadwalPemberkasan::class;
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

    public function getActiveSchedule()
    {
        return $this->model->where('status', 'aktif')
            ->whereDate('periode_awal', '<=', now())
            ->whereDate('periode_akhir', '>=', now())
            ->first();
    }
}
<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{Admin};
    
class AdminRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Admin::class;
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

    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function getById(int $adminId)
    {
        return $this->model->where('id', $adminId)->with('user')->first();
    }
}
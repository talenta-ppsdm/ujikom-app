<?php
    
namespace App\Repositories;
    
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\{User};
    
class UserRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
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

    public function getByNipAndName(string $nip, string $name)
    {
        return $this->model
        ->where('nip', $nip)
        ->whereRaw('LOWER(name) = ?', [strtolower($name)])
        ->first();
    }
}
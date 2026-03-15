<?php
// app/Services/UserService.php
namespace App\Services;

use App\Models\User;
use App\Traits\UserFilterTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends BaseService
{
    use UserFilterTrait;

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        
        return $this->model->create($data);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->find($id);
        
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);
        
        return $user;
    }

    public function getPaginatedUsers(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();
        $query = $this->applyUserFilters($query, $filters);
        
        return $query->paginate($perPage);
    }
}
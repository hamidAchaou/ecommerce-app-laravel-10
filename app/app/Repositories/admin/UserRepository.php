<?php

namespace App\Repositories\admin;

use App\Models\User;
use App\Repositories\BaseRepository;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    /**
     * Specify the model class for the repository.
     */
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Get paginated users with roles and permissions, plus all roles and permissions for UI selection.
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getUsersWithRolesPermissionsPaginate(array $filters = [], int $perPage = 15): array
    {
        $query = $this->model->with(['roles', 'permissions']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $keyword = $filters['search'];
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $usersPaginated = $query->paginate($perPage);

        // Preload permissions via roles for each user to avoid N+1 queries
        $usersPaginated->getCollection()->transform(function ($user) {
            $user->permissions_via_roles = $user->getPermissionsViaRoles();
            return $user;
        });

        return [
            'users' => $usersPaginated,
            // 'roles' => Role::all(),
            // 'permissions' => Permission::all(),
        ];
    }

    /**
     * Assign roles and permissions to a user by syncing.
     *
     * @param int $userId
     * @param array $roles
     * @param array $permissions
     * @return User|null
     */
    public function syncRolesAndPermissions(int $userId, array $roles = [], array $permissions = [])
    {
        $user = $this->find($userId);

        if ($user) {
            $user->syncRoles($roles);
            $user->syncPermissions($permissions);
        }

        return $user;
    }
}
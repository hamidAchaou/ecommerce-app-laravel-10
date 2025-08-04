<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleRepository extends BaseRepository
{
    /**
     * Specify the model class for the repository.
     */
    protected function model(): string
    {
        return Role::class;
    }

    /**
     * Get all roles with their permissions and all available permissions.
     */
    public function getAll(array $filters = []): array
    {
        $query = $this->model->with('permissions');
    
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
    
        return [
            'roles' => $query->paginate(10),
            'permissions' => Permission::all(),
        ];
    }
      

    /**
     * Create a new role with permissions.
     */
    public function createAndSyncPermissions(array $data)
    {
        $role = $this->create([
            'name' => $data['name'],
        ]);
    
        if (!empty($data['permissions'])) {
            // Get permission names by IDs
            $permissionNames = Permission::whereIn('id', $data['permissions'])->pluck('name')->toArray();
    
            // Sync permissions one by one
            foreach ($permissionNames as $permissionName) {
                $role->givePermissionTo($permissionName);
            }
        }
    
        return $role;
    }    
    
}
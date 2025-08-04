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
    public function getAll(): array
    {
        return [
            'roles' => $this->model->with('permissions')->get(),
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
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }
}
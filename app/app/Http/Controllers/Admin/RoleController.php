<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    private RoleRepository $roleRepository;
    private PermissionRepository $permissionRepository;

    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $filters = $request->only('search');
        $data = $this->roleRepository->getAll($filters);
        return view('admin.roles.index', ['roles' => $data['roles']]);
    }

    /**
     * Show form for creating a new role.
     */
    public function create()
    {
        $permissions = $this->permissionRepository->all();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRole($request);

        $this->roleRepository->createAndSyncPermissions($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(int $id)
    {
        $role = $this->roleRepository->find($id);
        $permissions = $this->permissionRepository->all();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, int $id)
    {
        $validated = $this->validateRole($request, $id);

        $role = $this->roleRepository->update(['name' => $validated['name']], $id);

        $permissionIds = $validated['permissions'] ?? [];
        $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($permissionNames);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(int $id)
    {
        try {
            $role = $this->roleRepository->find($id);
            $role->syncPermissions([]); // Detach all permissions before deletion

            $this->roleRepository->delete($id);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to delete role (ID: {$id}): {$e->getMessage()}");

            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Failed to delete role.');
        }
    }

    /**
     * Validate role request data.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return array
     */
    private function validateRole(Request $request, int $id = null): array
    {
        $uniqueRule = $id
            ? "unique:roles,name,{$id}"
            : 'unique:roles,name';

        return $request->validate([
            'name' => ['required', 'string', $uniqueRule],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
    }
}
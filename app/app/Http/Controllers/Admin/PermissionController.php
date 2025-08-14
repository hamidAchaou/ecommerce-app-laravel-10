<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\admin\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private PermissionRepository $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of permissions.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    /**
     * Display a listing of permissions with optional search.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = $request->only('search');
    
        // Use paginated method and pass searchable fields
        $permissions = $this->permissionRepository->getAllPaginate(
            $filters,
            [],                 // eager loading relations if needed
            ['name'],           // searchable fields
            7                 // items per page
        );
    
        return view('admin.permissions.index', compact('permissions'));
    }
    

    /**
     * Show the form for creating a new permission.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            // add other validation rules if needed
        ]);

        $this->permissionRepository->create($validated);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $permission = $this->permissionRepository->find($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => "required|string|unique:permissions,name,{$id}",
            // add other validation rules if needed
        ]);

        $this->permissionRepository->update($validated, $id);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->permissionRepository->delete($id);
            return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            // Log error here if desired
            return redirect()->route('admin.permissions.index')->with('error', 'Failed to delete permission.');
        }
    }
}
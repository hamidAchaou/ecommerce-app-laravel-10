<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private UserRepository $userRepo;
    private RoleRepository $roleRepo;

    public function __construct(UserRepository $userRepo, RoleRepository $roleRepo)
    {
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * Display a listing of the users with roles and permissions.
     */
    public function index(Request $request)
    {
        $filters = $request->only('search');

        // Get paginated users with roles & permissions eager loaded
        $data = $this->userRepo->getUsersWithRolesPermissionsPaginate($filters, 2);

        // Get all roles & permissions for filtering or UI selection
        $rolesPermissions = $this->roleRepo->getAll();

        $data['roles'] = $rolesPermissions['roles'];
        $data['permissions'] = $rolesPermissions['permissions'];

        return view('admin.users.index', $data);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $rolesPermissions = $this->roleRepo->getAll();

        return view('admin.users.create', [
            'roles' => $rolesPermissions['roles'],
            'permissions' => $rolesPermissions['permissions'],
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $user = $this->userRepo->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->userRepo->syncRolesAndPermissions(
            $user->id,
            $validated['roles'] ?? [],
            $validated['permissions'] ?? []
        );

        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int $id)
    {
        $user = $this->userRepo->find($id);
        $rolesPermissions = $this->roleRepo->getAll();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $rolesPermissions['roles'],
            'permissions' => $rolesPermissions['permissions'],
        ]);
    }

    /**
     * Update the specified user and sync roles and permissions.
     */
    public function update(Request $request, int $id)
    {
        $user = $this->userRepo->find($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$id}"],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $this->userRepo->update($updateData, $id);

        $this->userRepo->syncRolesAndPermissions(
            $id,
            $validated['roles'] ?? [],
            $validated['permissions'] ?? []
        );

        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id)
    {
        $this->userRepo->delete($id);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Update only the roles and permissions of the user.
     */
    public function updateRolesPermissions(Request $request, int $userId)
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $this->userRepo->syncRolesAndPermissions(
            $userId,
            $validated['roles'] ?? [],
            $validated['permissions'] ?? []
        );

        return redirect()->route('admin.users.index')
                         ->with('success', 'Rôles et permissions mis à jour.');
    }
}
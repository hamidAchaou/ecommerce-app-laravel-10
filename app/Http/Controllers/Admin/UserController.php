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
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepo, RoleRepository $roleRepository)
    {
        $this->userRepo = $userRepo;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $filters = $request->only('search');
        $data = $this->userRepo->getUsersWithRolesPermissionsPaginate($filters, 15);

        $rolesPermissions = $this->roleRepository->getAll();
        $data['roles'] = $rolesPermissions['roles'];
        $data['permissions'] = $rolesPermissions['permissions'];

        return view('admin.users.index', $data);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $rolesPermissions = $this->roleRepository->getAll();

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

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Update the roles and permissions of a user.
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

        return redirect()->route('admin.users.index')->with('success', 'Rôles et permissions mis à jour.');
    }
}
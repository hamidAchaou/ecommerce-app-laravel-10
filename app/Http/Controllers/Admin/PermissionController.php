<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
//     protected $roleRepository;
//     protected $permissionRepository;
    
//    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
//    {
//        $this->roleRepository = $roleRepository;
//        $this->permissionRepository = $permissionRepository;
//    }

   public function index()
   {
    //    $data = $this->roleRepository->getAll();
    $data = [];
       return view('admin.roles.index', compact('data'));
   }
}
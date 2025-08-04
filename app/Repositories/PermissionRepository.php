<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository
{
    protected function model(): string
    {
        return Permission::class;
    }
}
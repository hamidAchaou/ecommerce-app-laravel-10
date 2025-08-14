<?php

namespace App\Repositories\admin;

use App\Repositories\BaseRepository;
use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository
{
    protected function model(): string
    {
        return Permission::class;
    }

    public function getAll(array $filters = [])
    {
        return $this->getAllPaginate(
            $filters,
            ['permissions'],          // eager load permissions relation, for example
            ['name'],                 // searchable fields
            10,                       // 10 items per page
            'name',                   // order by 'name'
            'asc'                     // ascending order
        );
    }
}
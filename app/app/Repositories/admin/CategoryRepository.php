<?php

namespace App\Repositories\admin;

use App\Models\Category;  // <-- Import Category model
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    /**
     * Specify the model class for the repository.
     */
    protected function model(): string
    {
        return Category::class;
    }

}
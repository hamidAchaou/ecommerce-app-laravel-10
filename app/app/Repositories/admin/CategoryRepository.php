<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected function model(): string
    {
        return Category::class;
    }

    /**
     * Get homepage categories
     */
    public function getHomepageCategories(int $limit = 6)
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get a category by its slug
     */
    public function getBySlug(string $slug): ?Category
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }
}
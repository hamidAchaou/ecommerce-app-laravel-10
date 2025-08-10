<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define parent categories
        $parentCategories = [
            'Electronics' => ['type' => 'product'],
            'Clothing'    => ['type' => 'product'],
            'Books'       => ['type' => 'product'],
        ];

        $parents = [];

        // Insert parent categories
        foreach ($parentCategories as $name => $data) {
            $parents[$name] = Category::updateOrCreate(
                ['name' => $name, 'type' => $data['type'], 'parent_id' => null],
                ['name' => $name, 'type' => $data['type'], 'parent_id' => null]
            );
        }

        // Define subcategories
        $subCategories = [
            'Electronics' => ['Smartphones', 'Laptops', 'Accessories'],
            'Clothing'    => ['Men', 'Women', 'Kids'],
            'Books'       => ['Fiction', 'Non-Fiction', 'Textbooks'],
        ];

        // Insert subcategories
        foreach ($subCategories as $parentName => $children) {
            $parent = $parents[$parentName] ?? null;
            if ($parent) {
                foreach ($children as $childName) {
                    Category::updateOrCreate(
                        ['name' => $childName, 'type' => 'product', 'parent_id' => $parent->id],
                        ['name' => $childName, 'type' => 'product', 'parent_id' => $parent->id]
                    );
                }
            }
        }
    }
}

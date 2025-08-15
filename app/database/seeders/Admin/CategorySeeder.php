<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $defaultImages = [
            'Electronics' => 'https://via.placeholder.com/400x300?text=Electronics',
            'Clothing' => 'https://via.placeholder.com/400x300?text=Clothing',
            'Books' => 'https://via.placeholder.com/400x300?text=Books',
            'Traditional Industry in Morocco' => 'https://via.placeholder.com/400x300?text=Moroccan+Crafts',
            'Smartphones' => 'https://via.placeholder.com/400x300?text=Smartphones',
            'Laptops' => 'https://via.placeholder.com/400x300?text=Laptops',
            'Accessories' => 'https://via.placeholder.com/400x300?text=Accessories',
            'Men' => 'https://via.placeholder.com/400x300?text=Men+Clothing',
            'Women' => 'https://via.placeholder.com/400x300?text=Women+Clothing',
            'Kids' => 'https://via.placeholder.com/400x300?text=Kids+Clothing',
            'Fiction' => 'https://via.placeholder.com/400x300?text=Fiction+Books',
            'Non-Fiction' => 'https://via.placeholder.com/400x300?text=Non-Fiction+Books',
            'Textbooks' => 'https://via.placeholder.com/400x300?text=Textbooks',
            'Handmade Carpets' => 'https://via.placeholder.com/400x300?text=Carpets',
            'Pottery & Ceramics' => 'https://via.placeholder.com/400x300?text=Pottery',
            'Leather Goods' => 'https://via.placeholder.com/400x300?text=Leather',
            'Traditional Clothing' => 'https://via.placeholder.com/400x300?text=Traditional+Clothing',
            'Jewelry & Accessories' => 'https://via.placeholder.com/400x300?text=Jewelry',
        ];

        $parentCategories = [
            'Electronics' => ['type' => 'product'],
            'Clothing' => ['type' => 'product'],
            'Books' => ['type' => 'product'],
            'Traditional Industry in Morocco' => ['type' => 'product'],
        ];

        $parents = [];
        foreach ($parentCategories as $name => $data) {
            $parents[$name] = Category::updateOrCreate(
                ['name' => $name, 'type' => $data['type'], 'parent_id' => null],
                [
                    'name' => $name,
                    'type' => $data['type'],
                    'parent_id' => null,
                    'image' => $defaultImages[$name] ?? null
                ]
            );
        }

        $subCategories = [
            'Electronics' => ['Smartphones', 'Laptops', 'Accessories'],
            'Clothing' => ['Men', 'Women', 'Kids'],
            'Books' => ['Fiction', 'Non-Fiction', 'Textbooks'],
            'Traditional Industry in Morocco' => [
                'Handmade Carpets',
                'Pottery & Ceramics',
                'Leather Goods',
                'Traditional Clothing',
                'Jewelry & Accessories'
            ],
        ];

        foreach ($subCategories as $parentName => $children) {
            $parent = $parents[$parentName] ?? null;
            if ($parent) {
                foreach ($children as $childName) {
                    Category::updateOrCreate(
                        ['name' => $childName, 'type' => 'product', 'parent_id' => $parent->id],
                        [
                            'name' => $childName,
                            'type' => 'product',
                            'parent_id' => $parent->id,
                            'image' => $defaultImages[$childName] ?? null
                        ]
                    );
                }
            }
        }
    }
}
<?php
namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Product::updateOrCreate(
                ['id' => $row['id'] ?? null],
                [
                    'title'       => $row['title'],
                    'description' => $row['description'] ?? null,
                    'price'       => $row['price'],
                    'stock'       => $row['stock'],
                    'category_id' => $row['category_id'] ?? null,
                ]
            );
        }
    }
}
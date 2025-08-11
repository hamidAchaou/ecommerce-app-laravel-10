<?php
namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::select('id', 'title', 'description', 'price', 'stock', 'category_id')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Description', 'Price', 'Stock', 'Category ID'];
    }
}
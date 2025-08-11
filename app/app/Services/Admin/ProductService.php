<?php
namespace App\Services\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use App\Repositories\admin\ProductRepository;

class ProductService
{
    protected $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function import($file)
    {
        Excel::import(new ProductsImport, $file);
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

}
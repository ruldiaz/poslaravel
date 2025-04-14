<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::all(['id','product_name','category_id','supplier_id','product_code','product_garage','product_image','product_store','buying_date','expire_date','buying_price','selling_price','created_at','updated_at']);
    }

    public function headings(): array {
        return [
        'id',
        'product_name',
        'category_id',
        'supplier_id',
        'product_code',
        'product_garage',
        'product_image',
        'product_store',
        'buying_date',
        'expire_date',
        'buying_price',
        'selling_price',
        'created_at',
        'updated_at'    
        ];
    }
}

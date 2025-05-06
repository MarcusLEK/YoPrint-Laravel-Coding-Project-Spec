<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithUpserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'unique_key' => mb_convert_encoding($row['unique_key'], 'UTF-8'),
            'product_title' => mb_convert_encoding($row['product_title'], 'UTF-8'),
            'product_description' => mb_convert_encoding($row['product_description'], 'UTF-8'),
            'style' => mb_convert_encoding($row['style'], 'UTF-8'),
            'sanmar_mainframe_color' => mb_convert_encoding($row['sanmar_mainframe_color'], 'UTF-8'),
            'size' => mb_convert_encoding($row['size'], 'UTF-8'),
            'color_name' => mb_convert_encoding($row['color_name'], 'UTF-8'),
            'piece_price' => mb_convert_encoding($row['piece_price'], 'UTF-8'),
        ]);
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'unique_key';
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

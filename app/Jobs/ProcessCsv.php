<?php

namespace App\Jobs;

use App\Consts\Status;
use App\Imports\ProductsImport;
use App\Models\FileUpload;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCsv implements ShouldQueue
{
    use Queueable;

    public $fileUpload;

    /**
     * Create a new job instance.
     */
    public function __construct($fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->fileUpload->update(['status' => Status::PROCESSING]);

        $productDataChunk = [];
        $handle = fopen(storage_path('app/private/' . $this->fileUpload->file_path), 'r');
        fgetcsv($handle); // skip header
        while (($row = fgetcsv($handle)) !== false) {
            $productDataChunk[] = [
                'unique_key' => mb_convert_encoding($row[0], 'UTF-8'),
                'product_title' => mb_convert_encoding($row[1], 'UTF-8'),
                'product_description' => mb_convert_encoding($row[2], 'UTF-8'),
                'style' => mb_convert_encoding($row[3], 'UTF-8'),
                'sanmar_mainframe_color' => mb_convert_encoding($row[28], 'UTF-8'),
                'size' => mb_convert_encoding($row[18], 'UTF-8'),
                'color_name' => mb_convert_encoding($row[14], 'UTF-8'),
                'piece_price' => mb_convert_encoding($row[21], 'UTF-8'),
            ];

            if (count($productDataChunk) === 1000) {
                Product::upsert($productDataChunk, uniqueBy: ['unique_key'], update: ['product_title', 'product_description', 'style', 'sanmar_mainframe_color', 'size', 'color_name', 'piece_price']);
                $productDataChunk = [];
            }

            if (!empty($productDataChunk)) {
                Product::upsert($productDataChunk, uniqueBy: ['unique_key'], update: ['product_title', 'product_description', 'style', 'sanmar_mainframe_color', 'size', 'color_name', 'piece_price']);
            }
        }
        fclose($handle);

        $this->fileUpload->update(['status' => Status::COMPLETED]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        $this->fileUpload->update(['status' => Status::FAILED]);
    }
}

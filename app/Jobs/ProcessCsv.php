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

        Collect(file(storage_path('app/private/' . $this->fileUpload->file_path)))
            ->skip(1)
            ->map(fn($lines) => str_getcsv($lines))
            ->map(fn($productData) => [
                'unique_key' => mb_convert_encoding($productData[0], 'UTF-8'),
                'product_title' => mb_convert_encoding($productData[1], 'UTF-8'),
                'product_description' => mb_convert_encoding($productData[2], 'UTF-8'),
                'style' => mb_convert_encoding($productData[3], 'UTF-8'),
                'sanmar_mainframe_color' => mb_convert_encoding($productData[28], 'UTF-8'),
                'size' => mb_convert_encoding($productData[18], 'UTF-8'),
                'color_name' => mb_convert_encoding($productData[14], 'UTF-8'),
                'piece_price' => mb_convert_encoding($productData[21], 'UTF-8'),
            ])->each(fn($productData) => Product::create($productData));

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

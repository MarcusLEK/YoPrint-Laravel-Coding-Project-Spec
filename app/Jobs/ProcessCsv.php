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

        Excel::import(new ProductsImport(), $this->fileUpload->file_path);

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

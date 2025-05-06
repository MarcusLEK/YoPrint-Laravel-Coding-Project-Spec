<?php

namespace App\Jobs;

use App\Consts\Status;
use App\Imports\ProductsImport;
use App\Models\FileUpload;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
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

        $numberOfProcesses = 2;

        // Create an array of callables for tasks
        $tasks = [];
        for ($i = 0; $i < $numberOfProcesses; $i++) {
            $tasks[] = function () use ($i, $numberOfProcesses) {
                $this->processChunk($i, $numberOfProcesses);
            };
        }

        // Run concurrent tasks (array of callables)
        Concurrency::run($tasks);

        $this->fileUpload->update(['status' => Status::COMPLETED]);
    }

    private function processChunk(int $i, int $numberOfProcesses): void
    {
        $handle = fopen(storage_path('app/private/' . $this->fileUpload->file_path), 'r');
        fgets($handle); // Skip header
        $currentLine = 0;
        $productData = [];

        while (($row = fgets($handle)) !== false) {
            if ($currentLine++ % $numberOfProcesses !== $i) {
                continue;
            }

            $row = str_getcsv($row);
            $productData[] = [
                mb_convert_encoding($row[0], 'UTF-8'),
                mb_convert_encoding($row[1], 'UTF-8'),
                mb_convert_encoding($row[2], 'UTF-8'),
                mb_convert_encoding($row[3], 'UTF-8'),
                mb_convert_encoding($row[28], 'UTF-8'),
                mb_convert_encoding($row[18], 'UTF-8'),
                mb_convert_encoding($row[14], 'UTF-8'),
                mb_convert_encoding($row[21], 'UTF-8'),
            ];

            if (count($productData) === 1000) {
                DB::table('products')->upsert(
                    $productData,
                    uniqueBy: ['unique_key'],
                    update: ['product_title', 'product_description', 'style', 'sanmar_mainframe_color', 'size', 'color_name', 'piece_price']
                );
                $productData = [];
            }
        }

        if (!empty($productData)) {
            DB::table('products')->upsert(
                $productData,
                uniqueBy: ['unique_key'],
                update: ['product_title', 'product_description', 'style', 'sanmar_mainframe_color', 'size', 'color_name', 'piece_price']
            );
        }

        fclose($handle);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        $this->fileUpload->update(['status' => Status::FAILED]);
    }
}

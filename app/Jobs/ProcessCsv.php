<?php

namespace App\Jobs;

use App\Models\FileUpload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessCsv implements ShouldQueue
{
    use Queueable;

    public FileUpload $fileUpload;

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

    }
}

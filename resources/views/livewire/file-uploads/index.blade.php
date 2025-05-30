<?php

use App\Jobs\ProcessCsv;
use App\Models\FileUpload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {

    use WithFileUploads;

    #[Validate('max:100000')]
    public $file;

    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'created_at', 'label' => 'Time', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'File Name', 'class' => 'w-64'],
            ['key' => 'status_text', 'label' => 'Status', 'class' => 'w-20'],
        ];
    }

    public function fileUploads(): Collection
    {
        return FileUpload::query()
            ->orderBy(...array_values($this->sortBy))
            ->get();
    }

    public function with(): array
    {
        return [
            'fileUploads' => $this->fileUploads(),
            'headers' => $this->headers()
        ];
    }

    public function save()
    {
        $fileUpload = FileUpload::create([
            'name' => $this->file->getClientOriginalName(),
            'status' => \App\Consts\Status::PENDING,
            'file_path' => $this->file->store(path: 'file'),
        ]);

//        Collect(file(storage_path('app/private/' . $fileUpload->file_path)))
//            ->skip(1)
//            ->map(fn($lines) => str_getcsv($lines))
//            ->dd();

        ProcessCsv::dispatch($fileUpload);
    }
}; ?>

<div wire:poll>
    <x-card class="mb-3" shadow>
        <x-form wire:submit="save" no-separator>
            <x-file wire:model="file" label="CSV File" hint="Only CSV" accept="application/csv"/>

            <x-slot:actions>
                <x-button label="Upload" class="btn-primary" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-form>
    </x-card>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$fileUploads" :sort-by="$sortBy"></x-table>
    </x-card>
</div>

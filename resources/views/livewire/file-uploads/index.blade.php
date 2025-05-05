<?php

use App\Models\FileUpload;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'created_at', 'label' => 'Time', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'File Name', 'class' => 'w-64'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-20'],
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
}; ?>

<div>
    <x-card class="mb-3" shadow>
        <x-file wire:model="file" label="Excel" hint="Only Excel" accept="application/pdf"/>
    </x-card>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$fileUploads" :sort-by="$sortBy"></x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                 @keydown.enter="$wire.drawer = false"/>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false"/>
        </x-slot:actions>
    </x-drawer>
</div>

<?php

namespace App\Models;

use App\Consts\Status;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $fillable = [
        'name',
        'status',
        'file_path',
    ];

    protected $appends = ['status_text'];

    public function getStatusTextAttribute()
    {
        $statuses = Status::all();

        return $statuses[$this->status];
    }
}

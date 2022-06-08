<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class eletronic extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $guarded = ['count'];

    public function StorageArea(): BelongsToMany
    {
        return $this->belongsToMany(StorageArea::class, 'electronic_storage_areas', 'electronic_id', 'storage_id');
    }
}

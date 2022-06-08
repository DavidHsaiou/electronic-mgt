<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StorageArea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function Electronic(): BelongsToMany
    {
        return $this->belongsToMany(eletronic::class);
    }
}

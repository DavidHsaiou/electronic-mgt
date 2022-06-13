<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function WorkState(): BelongsToMany
    {
        return $this->belongsToMany(WorkState::class, 'eletronics_work_states', 'electronic_id', 'work_state_id');
    }

    public function ElectronicType(): BelongsTo
    {
        return $this->belongsTo(ElectronicType::class, 'electronic_type');
    }
}

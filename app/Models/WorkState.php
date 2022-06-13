<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkState extends Model
{
    use HasFactory;

    public function Electronic(): BelongsToMany
    {
        return $this->belongsToMany(eletronic::class, 'eletronics_work_states', 'work_state_id', 'electronic_id');
    }
}

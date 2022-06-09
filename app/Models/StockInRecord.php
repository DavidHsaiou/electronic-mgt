<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInRecord extends Model
{
    use HasFactory;

    public function details() {
        return $this->hasMany(StockInRecordDetail::class, 'record_id');
    }
}

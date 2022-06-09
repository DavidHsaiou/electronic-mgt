<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInRecordDetail extends Model
{
    use HasFactory;

    protected $fillable = ['record_id', 'electric_id', 'original_price', 'count'];

    public function mainRecord() {
        return $this->belongsTo(StockInRecord::class, 'record_id');
    }

    public function useElectronic() {
        return $this->belongsTo(eletronic::class, 'electric_id');
    }
}

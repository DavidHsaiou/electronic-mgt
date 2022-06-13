<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutRecordDetail extends Model
{
    use HasFactory;

    protected $fillable = ['electric_id', 'single_price', 'count'];

    public function mainRecord() {
        return $this->belongsTo(StockOutRecord::class, 'record_id');
    }

    public function useElectronic() {
        return $this->belongsTo(eletronic::class, 'electric_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    use HasFactory;

    public function SupplyMerchant(): BelongsTo
    {
        return $this->belongsTo(SupplyManagement::class, 'supply_id');
    }

    public function StockInRecords() {
        return $this->hasMany(StockInRecord::class, 'purchase_id');
    }
}

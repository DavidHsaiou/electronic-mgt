<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutRecord extends Model
{
    use HasFactory;

    public function Details() {
        return $this->hasMany(StockOutRecordDetail::class, 'record_id');
    }

    public function ShippingType(): BelongsTo
    {
        return $this->belongsTo(shippingType::class, 'shipping_type');
    }

    public function BillType(): BelongsTo
    {
        return $this->belongsTo(BillType::class, 'bill_type');
    }

    public function StockOutType(): BelongsTo
    {
        return $this->belongsTo(StockOutType::class, 'stock_out_type');
    }

    public function SellChannelType(): belongsTo
    {
        return $this->belongsTo(SellChannel::class, 'sell_channel_type');
    }

}

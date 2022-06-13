<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shippingType extends Model
{
    use HasFactory;

    public function StockOutRecords() {
        return $this->hasMany(StockOutRecord::class, 'shipping_type');
    }
}

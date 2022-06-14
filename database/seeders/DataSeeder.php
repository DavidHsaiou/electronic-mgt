<?php

namespace Database\Seeders;

use App\Models\ElectronicType;
use App\Models\SellChannel;
use App\Models\ShippingType;
use App\Models\StockOutType;
use App\Models\WorkState;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSeeder extends Seeder {

    public function run() {
        DB::table('storage_areas')
            ->insert([
                'name' => '未分類',
                'status' => 1
            ]);
        DB::table('bill_types')
            ->insert([
                'name' => '二聯式發票',
                'status' => 1
            ]);
        DB::table('bill_types')
            ->insert([
                'name' => '三聯式發票',
                'status' => 1
            ]);
        $newStockOutType = new StockOutType();
        $newStockOutType->name = '維修';
        $newStockOutType->status = 1;
        $newStockOutType->save();
        SellChannel::create([
            'name' => '蝦皮',
            'status'=> 1
        ]);
        ShippingType::create([
            'name' => '到貨便',
            'status' => 1,
        ]);
        WorkState::create([
            'name' => '未分類',
            'status' => 1
        ]);
        ElectronicType::create([
            'TypeName' => '電容',
            'status' => 1,
            "sort" => 0,
        ]);
        ElectronicType::create([
            'TypeName' => '電阻',
            'status' => 1,
            "sort" => 1
        ]);
    }
}

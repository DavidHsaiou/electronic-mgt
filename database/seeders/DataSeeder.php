<?php

namespace Database\Seeders;

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
    }
}

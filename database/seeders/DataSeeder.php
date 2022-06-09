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
    }
}

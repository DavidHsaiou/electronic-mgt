<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_menu')->insert([
            'title' => '存放區管理',
            'uri' => '/storage-areas',
            'icon' => 'fa-tasks'
        ]);

        DB::table('admin_menu')->insert([
            'title' => '料表管理',
            'uri' => '/eletronics',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '入庫管理',
            'uri' => '/stock-in-records',
            'icon' => 'fa-tasks'
        ]);
    }
}

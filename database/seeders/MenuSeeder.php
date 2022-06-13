<?php

namespace Database\Seeders;

use App\Models\SellChannel;
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
            'title' => '入庫管理',
            'uri' => '/stock-in-records',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '料表管理',
            'uri' => '/eletronics',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '出庫管理',
            'uri' => '/stock-out-records',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '存放區管理',
            'uri' => '/storage-areas',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '發票類型',
            'uri' => '/bill-types',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '出庫類型',
            'uri' => '/stock-out-types',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '通路類型',
            'uri' => '/sell-channels',
            'icon' => 'fa-tasks'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '物流類型',
            'uri' => '/shipping-types',
            'icon' => 'fa-tasks'
        ]);
    }
}

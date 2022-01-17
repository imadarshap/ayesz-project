<?php

namespace Database\seeds;

use App\Helper\Helper;
use Illuminate\Database\Seeder;
use App\UserRight;

class AdminRightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = Helper::getModules();
        $check = UserRight::where('admin_id',1)->count();
        if($check==0){
            foreach($modules as $module){
                UserRight::create([
                    'admin_id' => 1,
                    'module' => $module['module'],
                    'rights'=>$module['rights']
                    ]);
            }
        }
    }
}
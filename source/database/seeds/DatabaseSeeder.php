<?php

use Database\seeds\AdminRightsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([AdminRightsSeeder::class]);
        $this->command->info('User Rights table seeded!');
    }
}

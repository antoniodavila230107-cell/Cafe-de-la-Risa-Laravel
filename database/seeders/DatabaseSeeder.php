<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ZoneTableSeeder::class,
            CouponSeeder::class,
            DeliveryOrderSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}

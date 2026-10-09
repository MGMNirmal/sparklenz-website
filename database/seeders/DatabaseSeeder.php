<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the rate card and site content. Demo concept work is only added
     * outside production; no admin account is created here — use
     * `php artisan sparklenz:create-admin` instead.
     */
    public function run(): void
    {
        $this->call([
            SiteContentSeeder::class,
            RateCardSeeder::class,
        ]);

        if (! app()->isProduction()) {
            $this->call(DemoContentSeeder::class);
        }
    }
}

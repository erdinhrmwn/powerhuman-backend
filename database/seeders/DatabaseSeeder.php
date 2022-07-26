<?php

namespace Database\Seeders;

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
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@powerhuman.net',
        ]);

        \App\Models\User::factory(20)->create();
        \App\Models\Company::factory(5)->create();
        \App\Models\Team::factory(30)->create();
        \App\Models\Role::factory(30)->create();
        \App\Models\Responsibility::factory(50)->create();
        \App\Models\Employee::factory(10000)->create();

        $this->call(UserCompanySeeder::class);
    }
}

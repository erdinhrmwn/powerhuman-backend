<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, \App\Models\Company::count()) as $company_id) {
            DB::table('company_user')->insert([
                'user_id' => 1,
                'company_id' => $company_id,
            ]);
        }

        foreach (range(2, \App\Models\User::count()) as $user_id) {
            DB::table('company_user')->insert([
                'user_id' => $user_id,
                'company_id' => rand(1, \App\Models\Company::count()),
            ]);
        }
    }
}

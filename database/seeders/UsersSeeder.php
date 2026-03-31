<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $superUser = User::create([
            'name'              => 'Samuel Nduati',
            'email'             => 'help@samnduati.com',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'active' => true,
        ]);

        $adminUser = User::create([
            'name'              => 'Richard Karangi',
            'email'             => 'helpdesk@subarukenya.com',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'active' => true,
        ]);

    }
}

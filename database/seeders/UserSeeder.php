<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            [
                'email' => 'admin01@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ],
            [
                'email' => 'admin02@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        ];

        DB::table('users')->insert($user);
    }
}

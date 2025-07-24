<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan lokal Indonesia untuk data lebih relevan

        // Admin user
        DB::table('m_users')->insert([
            'user_id' => 1, // Tetapkan ID 1 untuk admin
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'create_who' => 1, // Dibuat oleh dirinya sendiri
            'create_date' => now(),
        ]);

        // Beberapa user biasa
        for ($i = 2; $i <= 10; $i++) {
            DB::table('m_users')->insert([
                'user_id' => $i,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'create_who' => 1, // Dibuat oleh admin
                'create_date' => now(),
            ]);
        }
    }
}
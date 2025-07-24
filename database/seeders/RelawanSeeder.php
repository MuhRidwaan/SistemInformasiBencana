<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RelawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $userIds = DB::table('m_users')->pluck('user_id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('Pastikan UserTableSeeder sudah dijalankan.');
            return;
        }

        for ($i = 0; $i < 10; $i++) { // Buat 10 data relawan
            DB::table('m_relawan')->insert([
                'keahlian' => $faker->randomElement(['Medis', 'Logistik', 'Komunikasi', 'Pencarian & Penyelamatan', 'Dapur Umum']),
                'organisasi' => $faker->randomElement(['Palang Merah Indonesia', 'Basarnas', 'Tim SAR Swasta', 'Aksi Cepat Tanggap']),
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KerusakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $bencanaIds = DB::table('m_bencana')->pluck('bencana_id')->toArray();
        $userIds = DB::table('m_users')->pluck('user_id')->toArray();

        if (empty($bencanaIds) || empty($userIds)) {
            $this->command->warn('Pastikan BencanaSeeder dan UserTableSeeder sudah dijalankan.');
            return;
        }

        for ($i = 0; $i < 20; $i++) { // Buat 20 data kerusakan
            DB::table('m_kerusakan')->insert([
                'bencana_id' => $faker->randomElement($bencanaIds),
                'objek' => $faker->randomElement(['Rumah', 'Jalan', 'Jembatan', 'Bangunan Publik', 'Lahan Pertanian']),
                'tingkat_kerusakan' => $faker->randomElement(['Ringan', 'Sedang', 'Berat', 'Rusak Total']),
                'jumlah' => $faker->numberBetween(1, 100),
                'satuan' => $faker->randomElement(['Unit', 'Meter', 'Hektar']),
                'deskripsi' => $faker->paragraph(2),
                'tanggal_input' => $faker->dateTimeBetween('-1 year', 'now'),
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KorbanSeeder extends Seeder
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

        for ($i = 0; $i < 15; $i++) { // Buat 15 data korban
            DB::table('m_korban')->insert([
                'bencana_id' => $faker->randomElement($bencanaIds),
                'meninggal' => $faker->numberBetween(0, 5),
                'luka_berat' => $faker->numberBetween(0, 10),
                'luka_ringan' => $faker->numberBetween(0, 50),
                'hilang' => $faker->numberBetween(0, 3),
                'mengungsi' => $faker->numberBetween(0, 500),
                'terdampak' => $faker->numberBetween(50, 2000),
                'tanggal_input' => $faker->dateTimeBetween('-1 year', 'now'),
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
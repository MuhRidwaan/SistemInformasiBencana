<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LokasiPoskoSeeder extends Seeder
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

        for ($i = 0; $i < 5; $i++) { // Buat 5 data lokasi posko
            DB::table('m_lokasi_posko')->insert([
                'bencana_id' => $faker->randomElement($bencanaIds),
                'nama_posko' => 'Posko ' . $faker->city,
                'alamat_posko' => $faker->address,
                'latitude' => $faker->latitude($min = -8.5, $max = -6.0),
                'longitude' => $faker->longitude($min = 106.0, $max = 110.0),
                'kapasitas' => $faker->numberBetween(50, 500),
                'kontak_person' => $faker->name . ' (' . $faker->phoneNumber . ')',
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UpayaPenangananSeeder extends Seeder
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

        for ($i = 0; $i < 20; $i++) { // Buat 20 data upaya penanganan
            DB::table('m_upaya_penanganan')->insert([
                'bencana_id' => $faker->randomElement($bencanaIds),
                'instansi' => $faker->randomElement(['BNPB', 'BPBD', 'TNI', 'Polri', 'Relawan Lokal', 'NGO Internasional']),
                'jenis_upaya' => $faker->randomElement(['Evakuasi', 'Distribusi Logistik', 'Pencarian Korban', 'Perbaikan Infrastruktur', 'Pendampingan Psikososial']),
                'deskripsi' => $faker->paragraph(3),
                'tanggal_penanganan' => $faker->dateTimeBetween('-1 year', 'now'),
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
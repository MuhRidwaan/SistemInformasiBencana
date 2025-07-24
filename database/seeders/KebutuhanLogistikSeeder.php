<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KebutuhanLogistikSeeder extends Seeder
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

        for ($i = 0; $i < 25; $i++) { // Buat 25 data kebutuhan logistik
            $jumlahDibutuhkan = $faker->numberBetween(50, 1000);
            DB::table('m_kebutuhan_logistik')->insert([
                'bencana_id' => $faker->randomElement($bencanaIds),
                'jenis_kebutuhan' => $faker->randomElement(['Makanan Pokok', 'Air Bersih', 'Selimut', 'Obat-obatan', 'Tenda Pengungsian', 'Pakaian']),
                'jumlah_dibutuhkan' => $jumlahDibutuhkan,
                'satuan' => $faker->randomElement(['Kg', 'Liter', 'Pcs', 'Unit']),
                'jumlah_tersedia' => $faker->numberBetween(0, $jumlahDibutuhkan),
                'tanggal_update' => $faker->dateTimeBetween('-6 months', 'now'),
                'deskripsi' => $faker->paragraph(1),
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
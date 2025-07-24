<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BencanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil ID dari tabel master yang sudah di-seed
        $jenisBencanaIds = DB::table('m_jenis_bencana')->pluck('jenis_bencana_id')->toArray();
        $provinsiIds = DB::table('m_dis_provinsi')->pluck('provinsi_id')->toArray();
        $kotaIds = DB::table('m_dis_kota')->pluck('kota_id')->toArray();
        $kecamatanIds = DB::table('m_dis_kecamatan')->pluck('kecamatan_id')->toArray();
        $kelurahanIds = DB::table('m_dis_kelurahan')->pluck('kelurahan_id')->toArray();
        $userIds = DB::table('m_users')->pluck('user_id')->toArray();

        // Pastikan ada data di tabel master
        if (empty($jenisBencanaIds) || empty($provinsiIds) || empty($kotaIds) || empty($kecamatanIds) || empty($kelurahanIds) || empty($userIds)) {
            $this->command->warn('Pastikan seeder master seperti JenisBencanaSeeder, DisProvinsiSeeder, DisKotaSeeder, DisKecamatanSeeder, DisKelurahanSeeder, dan UserTableSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        for ($i = 0; $i < 10; $i++) { // Buat 10 data bencana
            DB::table('m_bencana')->insert([
                'jenis_bencana_id' => $faker->randomElement($jenisBencanaIds),
                'nama_bencana' => $faker->word . ' ' . $faker->randomElement(['Banjir', 'Longsor', 'Gempa']),
                'kronologis' => $faker->paragraph(3),
                'deskripsi' => $faker->paragraph(5),
                'tanggal_kejadian' => $faker->dateTimeBetween('-2 years', 'now'),
                'latitude' => $faker->latitude($min = -8.5, $max = -6.0), // Contoh koordinat di sekitar Jawa
                'longitude' => $faker->longitude($min = 106.0, $max = 110.0), // Contoh koordinat di sekitar Jawa
                'provinsi_id' => $faker->randomElement($provinsiIds),
                'kota_id' => $faker->randomElement($kotaIds),
                'kecamatan_id' => $faker->randomElement($kecamatanIds),
                'kelurahan_id' => $faker->randomElement($kelurahanIds),
                'create_who' => $faker->randomElement($userIds),
                'create_date' => now(),
            ]);
        }
    }
}
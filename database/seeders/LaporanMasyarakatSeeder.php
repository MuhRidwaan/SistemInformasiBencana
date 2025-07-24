<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LaporanMasyarakatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan lokal Indonesia untuk data lebih relevan

        // Ambil ID dari tabel master yang mungkin sudah di-seed
        $bencanaIds = DB::table('m_bencana')->pluck('bencana_id')->toArray();
        $userIds = DB::table('m_users')->pluck('user_id')->toArray();

        // Pastikan ada data di tabel master yang dibutuhkan
        // Jika bencana_id dan create_who/change_who bisa NULL sesuai DDL, maka tidak wajib ada
        // Tapi untuk data dummy yang realistis, lebih baik ada referensi
        if (empty($bencanaIds)) {
            $this->command->warn('Tabel m_bencana kosong. Laporan akan diisi dengan bencana_id NULL.');
            // Jika kamu ingin bencana_id selalu ada, kamu bisa throw exception atau return di sini.
        }
        if (empty($userIds)) {
            $this->command->warn('Tabel m_users kosong. Kolom create_who/change_who akan diisi dengan NULL.');
        }


        for ($i = 0; $i < 20; $i++) { // Buat 20 data laporan masyarakat dummy
            DB::table('t_laporan_masyarakat')->insert([
                'jenis_laporan'     => $faker->randomElement(['Kerusakan Infrastruktur', 'Kebutuhan Logistik', 'Korban Jiwa', 'Orang Hilang', 'Lingkungan', 'Fasilitas Umum']),
                'judul_laporan'     => $faker->sentence(5),
                'deskripsi_laporan' => $faker->paragraph(3),
                'tanggal_laporan'   => $faker->dateTimeBetween('-6 months', 'now'),
                'nama_pelapor'      => $faker->name,
                'kontak_pelapor'    => $faker->phoneNumber,
                'latitude'          => $faker->latitude($min = -8.5, $max = -6.0), // Contoh koordinat di sekitar Jawa
                'longitude'         => $faker->longitude($min = 106.0, $max = 110.0), // Contoh koordinat di sekitar Jawa
                'path_foto'         => $faker->imageUrl(640, 480, 'nature', true, 'laporan', false, 'jpg'), // Contoh URL gambar dummy
                'status_laporan'    => $faker->randomElement(['Pending', 'Diterima', 'Diproses', 'Selesai', 'Ditolak']),
                'bencana_id'        => !empty($bencanaIds) ? $faker->randomElement($bencanaIds) : null,
                'create_who'        => !empty($userIds) ? $faker->randomElement($userIds) : null,
                'create_date'       => now(),
                'change_who'        => null, // Awalnya null
                'change_date'       => null, // Awalnya null
            ]);
        }
    }
}

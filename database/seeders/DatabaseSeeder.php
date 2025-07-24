<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     // Panggil seeder modul izin terlebih dahulu
        //     PermissionModulesTableSeeder::class,
        //     // Lalu panggil seeder izin utama (jika ada)
        //     PermissionsTableSeeder::class,
        //     // Terakhir, panggil seeder untuk mengaitkan izin ke modul
        //     AssignPermissionModulesSeeder::class,
        // ]);

        $this->call([
            // Seeder untuk tabel master (atau yang tidak punya FK masuk)
            // UserTableSeeder::class,
           // JenisBencanaSeeder::class,
            // Jika kamu punya seeder untuk provinsi, kota, kecamatan, kelurahan
            // Tambahkan di sini, contoh:
            // DisProvinsiSeeder::class,
            // DisKotaSeeder::class,
            // DisKecamatanSeeder::class,
            // DisKelurahanSeeder::class,

            // Seeder untuk tabel inti (yang menjadi FK untuk tabel lain)
            //BencanaSeeder::class,

            // Seeder untuk tabel terkait bencana (yang punya bencana_id)
            //KerusakanSeeder::class,
          //  KebutuhanLogistikSeeder::class,
            //KorbanSeeder::class,
           // LokasiPoskoSeeder::class,
            //UpayaPenangananSeeder::class,
            //RelawanSeeder::class, // Relawan tidak terkait langsung dengan bencana_id, tapi bisa dipanggil di sini
            LaporanMasyarakatSeeder::class,
        ]);
    }
}
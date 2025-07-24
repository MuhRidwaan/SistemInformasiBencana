<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission; // Import model Permission

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Daftar semua izin yang digunakan di aplikasi, termasuk di menu sidebar
        $permissions = [
            // Izin untuk Dashboard (jika ada pembatasan khusus, tambahkan di sini)
            // ['name' => 'view-dashboard', 'description' => 'Melihat halaman dashboard'],

            // Izin untuk Manajemen Pengguna & Hak Akses
            ['name' => 'view-users', 'description' => 'Melihat daftar pengguna'],
            ['name' => 'create-users', 'description' => 'Menambah pengguna baru'],
            ['name' => 'edit-users', 'description' => 'Mengedit data pengguna'],
            ['name' => 'delete-users', 'description' => 'Menghapus pengguna'],

            ['name' => 'view-roles', 'description' => 'Melihat daftar peran'],
            ['name' => 'create-roles', 'description' => 'Menambah peran baru'],
            ['name' => 'edit-roles', 'description' => 'Mengedit data peran'],
            ['name' => 'delete-roles', 'description' => 'Menghapus peran'],

            ['name' => 'view-permissions', 'description' => 'Melihat daftar izin'],
            ['name' => 'create-permissions', 'description' => 'Menambah izin baru'],
            ['name' => 'edit-permissions', 'description' => 'Mengedit izin'],
            ['name' => 'delete-permissions', 'description' => 'Menghapus izin'],

            // Izin untuk Manajemen Bencana (asumsi)
            ['name' => 'view-disasters', 'description' => 'Melihat data bencana'],
            ['name' => 'create-disasters', 'description' => 'Menambah data bencana'],
            ['name' => 'edit-disasters', 'description' => 'Mengedit data bencana'],
            ['name' => 'delete-disasters', 'description' => 'Menghapus data bencana'],
            ['name' => 'view-disaster-types', 'description' => 'Melihat jenis bencana'],
            ['name' => 'create-disaster-types', 'description' => 'Menambah jenis bencana'],
            ['name' => 'edit-disaster-types', 'description' => 'Mengedit jenis bencana'],
            ['name' => 'delete-disaster-types', 'description' => 'Menghapus jenis bencana'],


            // Izin untuk Logistik & Sumber Daya (asumsi)
            ['name' => 'view-logistics', 'description' => 'Melihat kebutuhan logistik'],
            ['name' => 'create-logistics', 'description' => 'Menambah kebutuhan logistik'],
            ['name' => 'edit-logistics', 'description' => 'Mengedit kebutuhan logistik'],
            ['name' => 'delete-logistics', 'description' => 'Menghapus kebutuhan logistik'],

            ['name' => 'view-posts', 'description' => 'Melihat lokasi posko'],
            ['name' => 'create-posts', 'description' => 'Menambah lokasi posko'],
            ['name' => 'edit-posts', 'description' => 'Mengedit lokasi posko'],
            ['name' => 'delete-posts', 'description' => 'Menghapus lokasi posko'],

            ['name' => 'view-volunteers', 'description' => 'Melihat data relawan'],
            ['name' => 'create-volunteers', 'description' => 'Menambah data relawan'],
            ['name' => 'edit-volunteers', 'description' => 'Mengedit data relawan'],
            ['name' => 'delete-volunteers', 'description' => 'Menghapus data relawan'],

            // Izin untuk Data Referensi Wilayah (asumsi)
            ['name' => 'view-provinces', 'description' => 'Melihat data provinsi'],
            ['name' => 'create-provinces', 'description' => 'Menambah data provinsi'],
            ['name' => 'edit-provinces', 'description' => 'Mengedit data provinsi'],
            ['name' => 'delete-provinces', 'description' => 'Menghapus data provinsi'],

            ['name' => 'view-cities', 'description' => 'Melihat data kota/kabupaten'],
            ['name' => 'create-cities', 'description' => 'Menambah data kota/kabupaten'],
            ['name' => 'edit-cities', 'description' => 'Mengedit data kota/kabupaten'],
            ['name' => 'delete-cities', 'description' => 'Menghapus data kota/kabupaten'],

            ['name' => 'view-districts', 'description' => 'Melihat data kecamatan'],
            ['name' => 'create-districts', 'description' => 'Menambah data kecamatan'],
            ['name' => 'edit-districts', 'description' => 'Mengedit data kecamatan'],
            ['name' => 'delete-districts', 'description' => 'Menghapus data kecamatan'],

            ['name' => 'view-villages', 'description' => 'Melihat data kelurahan'],
            ['name' => 'create-villages', 'description' => 'Menambah data kelurahan'],
            ['name' => 'edit-villages', 'description' => 'Mengedit data kelurahan'],
            ['name' => 'delete-villages', 'description' => 'Menghapus data kelurahan'],

            // Izin untuk Laporan & Penanganan (asumsi)
            ['name' => 'view-community-reports', 'description' => 'Melihat laporan masyarakat'],
            ['name' => 'create-community-reports', 'description' => 'Menambah laporan masyarakat'],
            ['name' => 'edit-community-reports', 'description' => 'Mengedit laporan masyarakat'],
            ['name' => 'delete-community-reports', 'description' => 'Menghapus laporan masyarakat'],

            ['name' => 'view-damage-data', 'description' => 'Melihat data kerusakan'],
            ['name' => 'create-damage-data', 'description' => 'Menambah data kerusakan'],
            ['name' => 'edit-damage-data', 'description' => 'Mengedit data kerusakan'],
            ['name' => 'delete-damage-data', 'description' => 'Menghapus data kerusakan'],

            ['name' => 'view-victim-data', 'description' => 'Melihat data korban'],
            ['name' => 'create-victim-data', 'description' => 'Menambah data korban'],
            ['name' => 'edit-victim-data', 'description' => 'Mengedit data korban'],
            ['name' => 'delete-victim-data', 'description' => 'Menghapus data korban'],

            ['name' => 'view-handling-efforts', 'description' => 'Melihat upaya penanganan'],
            ['name' => 'create-handling-efforts', 'description' => 'Menambah upaya penanganan'],
            ['name' => 'edit-handling-efforts', 'description' => 'Mengedit upaya penanganan'],
            ['name' => 'delete-handling-efforts', 'description' => 'Menghapus upaya penanganan'],
        ];

        foreach ($permissions as $permission) {
            // Gunakan firstOrCreate untuk mencegah duplikasi jika seeder dijalankan berkali-kali
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}

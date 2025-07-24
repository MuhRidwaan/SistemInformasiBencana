<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermissionModule;

class PermissionModulesTableSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Manajemen Pengguna', 'description' => 'Modul untuk mengelola pengguna dan hak akses.'],
            ['name' => 'Manajemen Peran', 'description' => 'Modul untuk mengelola peran pengguna.'],
            ['name' => 'Manajemen Izin', 'description' => 'Modul untuk mengelola daftar izin.'],
            ['name' => 'Manajemen Bencana', 'description' => 'Modul untuk mengelola data bencana.'],
            ['name' => 'Logistik & Sumber Daya', 'description' => 'Modul untuk mengelola logistik dan sumber daya.'],
            ['name' => 'Data Referensi Wilayah', 'description' => 'Modul untuk mengelola data referensi wilayah.'],
            ['name' => 'Laporan & Penanganan', 'description' => 'Modul untuk mengelola laporan dan upaya penanganan.'],
            // Tambahkan modul lain sesuai kebutuhan
        ];

        foreach ($modules as $module) {
            PermissionModule::firstOrCreate(['name' => $module['name']], $module);
        }
    }
}
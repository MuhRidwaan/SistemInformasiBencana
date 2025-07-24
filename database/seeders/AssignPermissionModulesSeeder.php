<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\PermissionModule;
use Illuminate\Support\Str; // Import Str facade

class AssignPermissionModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Pastikan modul-modul sudah ada di database
        $this->call(PermissionModulesTableSeeder::class);

        // Ambil semua modul izin yang sudah ada di database dan indeks berdasarkan nama
        $permissionModules = PermissionModule::all()->keyBy('name');

        // Definisi mapping nama izin ke nama modul
        // Ini harus konsisten dengan logika pengelompokan di admin/permissions/index.blade.php
        $moduleMapping = [
            'Manajemen Pengguna' => ['view-users', 'create-users', 'edit-users', 'delete-users', 'export-users'],
            'Manajemen Peran' => ['view-roles', 'create-roles', 'edit-roles', 'delete-roles'],
            'Manajemen Izin' => ['view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions'],
            'Manajemen Modul Izin' => ['view-permission-modules', 'create-permission-modules', 'edit-permission-modules', 'delete-permission-modules'],
            'Manajemen Bencana' => ['view-disasters', 'create-disasters', 'edit-disasters', 'delete-disasters', 'view-disaster-types', 'create-disaster-types', 'edit-disaster-types', 'delete-disaster-types'],
            'Logistik & Sumber Daya' => ['view-logistics', 'create-logistics', 'edit-logistics', 'delete-logistics', 'view-posts', 'create-posts', 'edit-posts', 'delete-posts', 'view-volunteers', 'create-volunteers', 'edit-volunteers', 'delete-volunteers'],
            'Data Referensi Wilayah' => ['view-provinces', 'create-provinces', 'edit-provinces', 'delete-provinces', 'view-cities', 'create-cities', 'edit-cities', 'delete-cities', 'view-districts', 'create-districts', 'edit-districts', 'delete-districts', 'view-villages', 'create-villages', 'edit-villages', 'delete-villages'],
            'Laporan & Penanganan' => ['view-community-reports', 'create-community-reports', 'edit-community-reports', 'delete-community-reports', 'view-damage-data', 'create-damage-data', 'edit-damage-data', 'delete-damage-data', 'view-victim-data', 'create-victim-data', 'edit-victim-data', 'delete-victim-data', 'view-handling-efforts', 'create-handling-efforts', 'edit-handling-efforts', 'delete-handling-efforts'],
        ];

        // Ambil semua izin yang sudah ada di database
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $assignedModuleId = null;
            $foundModuleName = null; // Tambahkan variabel untuk menyimpan nama modul yang ditemukan

            foreach ($moduleMapping as $moduleName => $prefixes) {
                foreach ($prefixes as $prefix) {
                    if (Str::startsWith($permission->name, $prefix)) {
                        // Jika nama izin cocok dengan prefix modul, ambil module_id-nya
                        if (isset($permissionModules[$moduleName])) {
                            $assignedModuleId = $permissionModules[$moduleName]->module_id;
                            $foundModuleName = $moduleName; // Simpan nama modul
                            break 2; // Keluar dari kedua loop inner
                        } else {
                            // Log jika modul tidak ditemukan di database meskipun ada di mapping
                            $this->command->warn("Module '{$moduleName}' for permission '{$permission->name}' not found in database. Skipping assignment.");
                        }
                    }
                }
            }

            // Perbarui module_id izin jika ditemukan mappingnya dan belum terisi
            if ($assignedModuleId !== null && $permission->module_id !== $assignedModuleId) {
                $permission->module_id = $assignedModuleId;
                $permission->save();
                // Perbaikan: Tambahkan null check untuk $foundModuleName
                $this->command->info("Assigned permission '{$permission->name}' to module '" . ($foundModuleName ?? 'Unknown Module') . "'.");
            } elseif ($assignedModuleId === null && $permission->module_id !== null) {
                // Jika izin tidak lagi cocok dengan modul yang didefinisikan, set ke null
                $permission->module_id = null;
                $permission->save();
                $this->command->info("Removed module from permission '{$permission->name}'.");
            }
        }

        $this->command->info('Module IDs assigned to existing permissions.');
    }
}

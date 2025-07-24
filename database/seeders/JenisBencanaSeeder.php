<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisBencanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisBencana = [
            ['jenis_bencana_id' => 1, 'nama_jenis' => 'Banjir', 'deskripsi_jenis' => 'Genangan air dalam jumlah besar.'],
            ['jenis_bencana_id' => 2, 'nama_jenis' => 'Tanah Longsor', 'deskripsi_jenis' => 'Gerakan massa tanah atau batuan.'],
            ['jenis_bencana_id' => 3, 'nama_jenis' => 'Gempa Bumi', 'deskripsi_jenis' => 'Getaran pada permukaan bumi.'],
            ['jenis_bencana_id' => 4, 'nama_jenis' => 'Angin Puting Beliung', 'deskripsi_jenis' => 'Angin berputar kencang.'],
            ['jenis_bencana_id' => 5, 'nama_jenis' => 'Kebakaran Hutan', 'deskripsi_jenis' => 'Api yang menyebar tak terkendali di area hutan.'],
            ['jenis_bencana_id' => 6, 'nama_jenis' => 'Erupsi Gunung Berapi', 'deskripsi_jenis' => 'Letusan gunung berapi.'],
        ];

        foreach ($jenisBencana as $data) {
            DB::table('m_jenis_bencana')->insert(array_merge($data, [
                'create_who' => 1, // Dibuat oleh admin
                'create_date' => now(),
            ]));
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // truncate tables
        DB::statement('TRUNCATE TABLE tpa_types RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE settlement_types RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE sample_methods RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE building_types RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE floor_types RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE location_types RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE environment_types RESTART IDENTITY CASCADE');

        // tpa types: INDOOR, OUTDOOR
        DB::table('tpa_types')->insert([
            ['name' => 'INDOOR'],
            ['name' => 'OUTDOOR'],
        ]);

        // settlement types: Pantai, Kota Padat, Kota Tidak Padat, Perkotaan, Pedesaan
        DB::table('settlement_types')->insert([
            ['name' => 'Pantai'],
            ['name' => 'Kota Padat'],
            ['name' => 'Kota Tidak Padat'],
            ['name' => 'Perkotaan'],
            ['name' => 'Pedesaan'],
        ]);

        // sample methods: Metode 1, Metode 2, Metode 3
        DB::table('sample_methods')->insert([
            ['name' => 'Metode 1'],
            ['name' => 'Metode 2'],
            ['name' => 'Metode 3'],
        ]);

        // building types: Rumah, Kantor, Sekolah, Tempat Ibadah
        DB::table('building_types')->insert([
            ['name' => 'Rumah'],
            ['name' => 'Kantor'],
            ['name' => 'Sekolah'],
            ['name' => 'Tempat Ibadah']
        ]);

        // floor type: Permanen, Semi Permanen, Sementara, Semen, Tanah
        DB::table('floor_types')->insert([
            ['name' => 'Permanen'],
            ['name' => 'Semi Permanen'],
            ['name' => 'Sementara'],
            ['name' => 'Semen'],
            ['name' => 'Tanah'],
        ]);

        // location types: Pasar, Sekolah, Pesantren, SPBU, Rumah Sakit, Kantor, Tempat Ibadah, Pusat Perbelanjaan, Tempat Hiburan, Tempat Olahraga, Tempat Wisata, Tempat Pemakaman, Fasilitas Pelayanan Kesehatan, Warung, Rumah
        DB::table('location_types')->insert([
            ['name' => 'Pasar'],
            ['name' => 'Sekolah'],
            ['name' => 'Pesantren'],
            ['name' => 'SPBU'],
            ['name' => 'Rumah Sakit'],
            ['name' => 'Kantor'],
            ['name' => 'Tempat Ibadah'],
            ['name' => 'Pusat Perbelanjaan'],
            ['name' => 'Tempat Hiburan'],
            ['name' => 'Tempat Olahraga'],
            ['name' => 'Tempat Wisata'],
            ['name' => 'Tempat Pemakaman'],
            ['name' => 'Fasilitas Pelayanan Kesehatan'],
            ['name' => 'Warung'],
            ['name' => 'Rumah'],
        ]);

        // environment types: Kumuh, Bersih, Tidak Bersih
        DB::table('environment_types')->insert([
            ['name' => 'Kumuh'],
            ['name' => 'Bersih'],
            ['name' => 'Tidak Bersih'],
        ]);
    }
}

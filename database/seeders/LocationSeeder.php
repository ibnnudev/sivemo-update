<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('TRUNCATE TABLE provinces RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE regencies RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE districts RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE villages RESTART IDENTITY CASCADE');

        // read csv file and insert data into database
        $file = fopen(database_path('dumps/provinces.csv'), 'r');
        $data = [];
        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $data[] = "('" . pg_escape_string((string) $row[0]) . "', '" . pg_escape_string($row[1]) . "')";
        }
        fclose($file);
        DB::statement("INSERT INTO provinces (id, name) VALUES " . implode(',', $data));

        // read csv file and insert data into database
        $file = fopen(database_path('dumps/regencies.csv'), 'r');
        $data = [];
        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $data[] = "('" . pg_escape_string((string) $row[0]) . "', '" . pg_escape_string((string) $row[1]) . "', '" . pg_escape_string($row[2]) . "')";
        }
        fclose($file);
        DB::statement("INSERT INTO regencies (id, province_id, name) VALUES " . implode(',', $data));

        // read csv file and insert data into database
        $file = fopen(database_path('dumps/districts.csv'), 'r');
        $data = [];
        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $data[] = "('" . pg_escape_string((string) $row[0]) . "', '" . pg_escape_string((string) $row[1]) . "', '" . pg_escape_string($row[2]) . "')";
        }
        fclose($file);
        DB::statement("INSERT INTO districts (id, regency_id, name) VALUES " . implode(',', $data));

        // read csv file and insert data into database
        $file = fopen(database_path('dumps/villages.csv'), 'r');
        $data = [];
        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $data[] = "('" . pg_escape_string((string) $row[0]) . "', '" . pg_escape_string((string) $row[1]) . "', '" . pg_escape_string($row[2]) . "')";
        }
        fclose($file);
        $chunks = array_chunk($data, 5000);
        foreach ($chunks as $chunk) {
            DB::statement("INSERT INTO villages (id, district_id, name) VALUES " . implode(',', $chunk));
        }
    }
}

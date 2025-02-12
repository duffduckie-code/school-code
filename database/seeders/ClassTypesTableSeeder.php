<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_types')->delete();

        $data = [
            ['name' => 'Form 1', 'code' => 'F1'],
            ['name' => 'Form 2', 'code' => 'F2'],
            ['name' => 'Form 3', 'code' => 'F3'],
            ['name' => 'Form 4', 'code' => 'F4'],
            ['name' => 'Form 5', 'code' => 'F5'],
            ['name' => 'Form 6', 'code' => 'F6'],
        ];

        DB::table('class_types')->insert($data);

    }
}

<?php
namespace Database\Seeders;

use App\Models\ClassType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MyClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('my_classes')->delete();
        $ct = ClassType::pluck('id')->all();

        $data = [
            ['name' => 'FORM 1 A', 'class_type_id' => $ct[0]],
            ['name' => 'FORM 1 B', 'class_type_id' => $ct[0]],
            ['name' => 'FORM 2 A', 'class_type_id' => $ct[1]],
            ['name' => 'FORM 2 B', 'class_type_id' => $ct[1]],
            ['name' => 'FORM 3 A', 'class_type_id' => $ct[2]],
            ['name' => 'FORM 3 B', 'class_type_id' => $ct[2]],
            ['name' => 'FORM 4 A', 'class_type_id' => $ct[3]],
            ['name' => 'FORM 4 B', 'class_type_id' => $ct[3]],
            ['name' => 'FORM 5 ARTS', 'class_type_id' => $ct[4]],
            ['name' => 'FORM 5 COMMERCIALS', 'class_type_id' => $ct[4]],
            ['name' => 'FORM 5 SCIENCES', 'class_type_id' => $ct[4]],
            ['name' => 'FORM 6 ARTS', 'class_type_id' => $ct[5]],
            ['name' => 'FORM 6 COMMERCIALS', 'class_type_id' => $ct[5]],
            ['name' => 'FORM 6 SCIENCES', 'class_type_id' => $ct[5]],
            ];

        DB::table('my_classes')->insert($data);

    }
}

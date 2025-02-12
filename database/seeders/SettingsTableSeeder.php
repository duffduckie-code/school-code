<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();

        $data = [
            ['type' => 'current_session', 'description' => '2025-2026'],
            ['type' => 'system_title', 'description' => 'SMS SUDO SYSTEMS'],
            ['type' => 'system_name', 'description' => 'SMS'],
            ['type' => 'term_ends', 'description' => '30/03/2025'],
            ['type' => 'term_begins', 'description' => '05/01/2025'],
            ['type' => 'phone', 'description' => '0784250065'],
            ['type' => 'address', 'description' => 'BULAWAYO'],
            ['type' => 'system_email', 'description' => 'info@sudosystems.co.zw'],
            ['type' => 'alt_email', 'description' => ''],
            ['type' => 'email_host', 'description' => ''],
            ['type' => 'email_pass', 'description' => ''],
            ['type' => 'lock_exam', 'description' => 0],
            ['type' => 'logo', 'description' => ''],
            ['type' => 'next_term_fees_f1', 'description' => '150'],
            ['type' => 'next_term_fees_f2', 'description' => '200'],
            ['type' => 'next_term_fees_f3', 'description' => '250'],
            ['type' => 'next_term_fees_f4', 'description' => '300'],
            ['type' => 'next_term_fees_f5', 'description' => '350'],
            ['type' => 'next_term_fees_f6', 'description' => '400'],
        ];

        DB::table('settings')->insert($data);

    }
}

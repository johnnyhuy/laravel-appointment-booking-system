<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TempPassword extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('temp_password')->insert([
            'password' => 'sept123',
            'used' => 0
        ]);
    }
}

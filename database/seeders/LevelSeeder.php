<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('levels')->insert([
            'name' => 'Low',
        ]);
        DB::table('levels')->insert([
            'name' => 'Medium',
        ]);
        DB::table('levels')->insert([
            'name' => 'High',
        ]);
    }
}

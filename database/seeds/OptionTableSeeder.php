<?php

use Illuminate\Database\Seeder;
use App\Models\Option;

class OptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Option::truncate();
        factory(Option::class, 20)->create();
    }
}

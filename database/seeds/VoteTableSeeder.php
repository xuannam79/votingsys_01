<?php

use Illuminate\Database\Seeder;
use App\Models\Vote;

class VoteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vote::truncate();
        factory(Vote::class, 20)->create();
    }
}

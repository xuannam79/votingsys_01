<?php

use Illuminate\Database\Seeder;
use App\Models\Poll;

class PollTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Poll::truncate();
        factory(Poll::class, 5)->create();
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Models\ParticipantVote;

class ParticipantVoteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ParticipantVote::truncate();
        factory(ParticipantVote::class, 10)->create();
    }
}

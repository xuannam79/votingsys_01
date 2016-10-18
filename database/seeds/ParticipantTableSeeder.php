<?php

use Illuminate\Database\Seeder;
use App\Models\Participant;

class ParticipantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Participant::truncate();
        factory(Participant::class, 10)->create();
    }
}

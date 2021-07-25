<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Suit;

class SuitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suits =  [
            ['name' => '♥'],
            ['name' => '♣'],
            ['name' => '♦'],
            ['name' => '♠']
        ];

        foreach ($suits as $suit) {
            Suit::create($suit);
        }
    }
}

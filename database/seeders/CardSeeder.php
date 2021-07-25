<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Suit;
use App\Models\Card;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names =  [
            ['name' => '2'],
            ['name' => '3'],
            ['name' => '4'],
            ['name' => '5'],
            ['name' => '6'],
            ['name' => '7'],
            ['name' => '8'],
            ['name' => '9'],
            ['name' => '10'],
            ['name' => 'J'],
            ['name' => 'Q'],
            ['name' => 'K'],
            ['name' => 'A']
        ];

        foreach ($names as $name) {
            $card = Card::create($name);

            Suit::All()->each(function ($suit) use ($card) {
                $suit->cards()->attach($card);
            });
        }
    }
}

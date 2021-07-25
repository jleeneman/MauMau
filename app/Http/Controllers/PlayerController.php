<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPlayers()
    {
        $players = Player::inRandomOrder()
            ->limit(4)
            ->get();

        return $players;
    }
}

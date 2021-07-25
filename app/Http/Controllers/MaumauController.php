<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\PlayerController;
use App\Models\Card;
use App\Models\Suit;

class MaumauController extends Controller
{
    // Drawing pile
    private const drawingPile = array();

    // Discard pile
    private const discardPile = array();

    // Game is being played
    public $playing = true;

    // Players
    public $players;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->startGame();
    }

    /**
     * Starts game
     */
    public function startGame() {
        // Get players
        $players = $this->getPlayers();

        // Deal cards to the players
        $this->dealCards($players);

        // Starts the game with a starting play
        $this->beforePlayGame();

        $this->playGame($players);
    }

    /**
     * Stops game
     */
    public function stopGame() {
        $this->playing = false;
    }

    /**
     * Get cards with relation suits
     */
    protected function getCards() {
        $cards = Card::with('suits')->get();
        $suits = Suit::get('id');
        $cardsArray = [];

        foreach ($cards as $card) {
            foreach ($suits as $index => $suit) {
                $cardsArray[] = [
                    'name' => $card->name,
                    'suit' => $card->suits[$index]->name
                ];
            }
        }

        return $cardsArray;
    }

    /**
     * Gets cards and shuffle
     */
    protected function getShuffledCards() {
        $cards = $this->getCards();

        shuffle($cards);

        return $cards;
    }

    /**
     * Deals cards to the players
     */
    protected function dealCards($players) {
        $cards = $this->getShuffledCards();

        foreach ($players as $player) {
            $player->cards = array_slice($cards, 0, 7);

            $dealString = $player->name . " has been dealt:";
            foreach ($player->cards as $pcard) {
                $dealString .= " " . $pcard['suit'] . " " . $pcard['name'];

                $pos = array_search($pcard, $cards);
                unset($cards[$pos]);
            }

            error_log($dealString);
        }

        $this->drawingPile = $cards;
    }

    /**
     * Starting play of the game
     * Get first item from drawing pile and throw on the discard pile
     */
    protected function beforePlayGame() {
        $firstKey = array_key_first($this->drawingPile);

        error_log("Top card is: " . $this->drawingPile[$firstKey]['suit'] . $this->drawingPile[$firstKey]['name']);

        $this->discardPile[] = $this->drawingPile[$firstKey];
        unset($this->drawingPile[$firstKey]);
    }

    /**
     * Playing game
     */
    protected function playGame($players) {

        while ($this->playing) {

            foreach ($players as $player) {

                if ($player->cards) {
                    $discardTop = $this->getDiscardTop();

                    $cardPlayed = false;

                    foreach ($player->cards as $key => $pcard) {

                        if ($discardTop['name'] == $pcard['name']) {

                            $this->discardPile[] = $player->cards[$key];

                            $playerCards = $player->cards;
                            unset($playerCards[$key]);

                            $player->cards = $playerCards;

                            $cardPlayed = true;

                            error_log($player->name . " plays " . $pcard['suit'] . $pcard['name']);

                            break;
                        } elseif ($discardTop['suit'] == $pcard['suit']) {
                            $this->discardPile[] = $player->cards[$key];

                            $playerCards = $player->cards;
                            unset($playerCards[$key]);

                            $player->cards = $playerCards;

                            $cardPlayed = true;

                            error_log($player->name . " plays " . $pcard['suit'] . $pcard['name']);

                            break;
                        }
                    }

                    if ($cardPlayed == false) {
                        $firstKey = array_key_first($this->drawingPile);

                        $playerCards = $player->cards;

                        if (empty($this->drawingPile)) {
                            $discardLength = count($this->discardPile);

                            $this->drawingPile[] = array_slice($this->discardPile, 1, $discardLength);

                            foreach ($this->drawingPile as $drawingCard) {
                                $pos = array_search($drawingCard, $this->discardPile);
                                unset($this->discardPile[$pos]);
                            }
                        }
                            
                        $playerCards[] = $this->drawingPile[$firstKey];
                        $player->cards = $playerCards;

                        error_log($player->name . " does not have a suitable card, taking from deck " . $this->drawingPile[$firstKey]['suit'] . $this->drawingPile[$firstKey]['name']);

                        unset($this->drawingPile[$firstKey]);
                        
                        continue;
                    }

                    if (count($player->cards) == 1) {
                        error_log($player->name . " has 1 card remaining");
                    } elseif (count($player->cards) == 0) {
                        error_log($player->name . " has won");
                        $this->stopGame();
                        break;
                    }
                }

            }

        }

    }

    /**
     * Gets latest added item from discard pile
     */
    protected function getDiscardTop() {
        return end($this->discardPile);
    }

    /**
     * Gets players from PlayerController
     */
    protected function getPlayers() {
        $players = (new PlayerController)->getPlayers();

        $consoleString = 'Starting game with ';

        $count = count($players);

        $i = 0;
        foreach ($players as $key => $player) {
            $i++;

            if ($i !== $count) {
                $consoleString .= $player->name . ', ';
                
            } else {
                $consoleString .= $player->name;
            }

        }
        error_log($consoleString);

        return $players;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardSuitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_suit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('suit_id')->unsigned();
            $table->foreign('suit_id')->references('id')->on('suits');
            $table->bigInteger('card_id')->unsigned();
            $table->foreign('card_id')->references('id')->on('cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_suit', function (Blueprint $table) {
            $table->dropForeign('card_suit_suit_id_foreign');
            $table->dropForeign('card_suit_card_id_foreign');
            $table->dropIndex('card_suit_suit_id_foreign');
            $table->dropIndex('card_suit_card_id_foreign');
            $table->dropColumn('suit_id');
            $table->dropColumn('card_id');
        });    
   }
}

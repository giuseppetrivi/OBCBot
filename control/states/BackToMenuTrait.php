<?php

use OBCBot\entities\api_cotrap\Search;
use OBCBot\entities\api_cotrap\SearchHistory;
use OBCBot\view\InlineKeyboards;
use OBCBot\view\Keyboards;
use OBCBot\view\MainTextMessages;

/**
 * Procedure to come back to the main menu, common to every process
 */
trait BackToMenuTrait {
  protected function backToMenuProcedure() {
    $_Search = new Search($this->_User->getUserId());
    $_Search->destroySearch();

    $this->_Bot->sendMessage([
      'text' => MainTextMessages::mainMenu(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    
    /*$_SearchHistory = new SearchHistory($this->_User->getUserId());
    $most_frequent_routes = $_SearchHistory->getMostFrequentRoutes();

    $this->_Bot->sendMessage([
      'text' => MainTextMessages::chooseBetweenMostFrequentRoutes(),
      'reply_markup' => InlineKeyboards::mostFrequentRoutesList($most_frequent_routes)
    ]);*/

    $this->setNextState(NULL);
  }
}


?>
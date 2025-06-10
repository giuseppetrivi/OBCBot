<?php

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationsU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\entities\api_cotrap\SearchHistory;
use CustomBotName\entities\api_cotrap\SearchU;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\Keyboards;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\MainTextMessages;
use CustomBotName\view\SearchEUTextMessages;
use CustomBotName\view\SearchUTextMessages;

class Main extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::COMMAND_START => "startProcedure",
    MenuOptions::SEARCH_EU => "searchEuProcedure",
    MenuOptions::SEARCH_U => "searchUProcedure"
  ];


  /**
   * States:
   * NULL (Main) -> NULL (Main)
   */
  protected function startProcedure() {
    $_SearchHistory = new SearchHistory($this->_User->getUserId());
    $most_frequent_routes = $_SearchHistory->getMostFrequentRoutes();

    $this->_Bot->sendMessage([
      'text' => MainTextMessages::welcome($this->_Bot->getChatWithChecks()->getUsername()),
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    /*$this->_Bot->sendMessage([
      'text' => MainTextMessages::chooseBetweenMostFrequentRoutes(),
      'reply_markup' => InlineKeyboards::mostFrequentRoutesList($most_frequent_routes)
    ]);*/
  }


  /**
   * States:
   * NULL (Main) -> SearchEU\DepartureLocation
   */
  protected function searchEuProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $result = $_SearchEU->initializeSearch();

    if ($result!=1) {
      $_SearchEU->destroySearch();
      $_SearchEU->initializeSearch();
    }

    $this->_Bot->sendMessage([
      'text' => SearchEUTextMessages::chooseLocation(true),
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState("SearchEU\DepartureLocation");
  }


  /**
   * States:
   * NULL (Main) -> SearchU\DepartureLocation
   */
  protected function searchUProcedure() {
    $_SearchU = new SearchU($this->_User->getUserId());
    $result = $_SearchU->initializeSearch();

    if ($result!=1) {
      $_SearchU->destroySearch();
      $_SearchU->initializeSearch();
    }

    $_LocationsU = new LocationsU();
    $all_urbal_locations = $_LocationsU->getAllUrbanLocations();

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::urbanSearchHeader(),
      'reply_markup' => Keyboards::getBackAndMenu()
    ]);

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::chooseUrbanLocation(),
      'reply_markup' => InlineKeyboards::urbanLocationsList($all_urbal_locations)
    ]);

    $this->setNextState("SearchU\DepartureLocation");
  }


}

?>
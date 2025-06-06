<?php

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationsU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\entities\api_cotrap\SearchU;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;


class Main extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::COMMAND_START => "startProcedure",
    MenuOptions::SEARCH_EU => "searchEuProcedure",
    MenuOptions::SEARCH_U => "searchUProcedure",
    MenuOptions::SETTINGS => "settingsProcedure",
  ];


  /**
   * States:
   * NULL (Main) -> NULL (Main)
   */
  protected function startProcedure() {
    $this->_Bot->sendMessage([
      'text' => TextMessages::startingMessage($this->_Bot->getChatWithChecks()->getUsername()),
      'reply_markup' => Keyboards::getMainMenu()
    ]);
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
      'text' => TextMessages::chooseDepartureLocation(),
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
      'text' => TextMessages::urbanSearchHeader(),
      'reply_markup' => Keyboards::getBackAndMenu()
    ]);

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseUrbanLocation(),
      'reply_markup' => InlineKeyboards::urbanLocationsList($all_urbal_locations)
    ]);

    $this->setNextState("SearchU\DepartureLocation");
  }

  /**
   * States:
   * NULL (Main) -> Settings
   */
  protected function settingsProcedure() {
    // TODO
  }


}

?>
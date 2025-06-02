<?php

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

/**
 * 
 */
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
   * NULL (Main) -> SearchU\???
   */
  protected function searchUProcedure() {
    // TODO
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
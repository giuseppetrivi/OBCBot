<?php

use CustomBotName\view;
use CustomBotName\control\AbstractState;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;


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
   * NULL (Main) -> SearchEU\DepartureLocation
   *                SearchU\???
   *                Settings
   */
  protected function startProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Questo è il messaggio di start",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


  /**
   * States:
   * NULL (Main) -> SearchEU\DepartureLocation
   */
  protected function searchEuProcedure() {
    $this->_Bot->sendMessage([
      'text' => "➤ Invia il nome della località di <u>partenza</u>",
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState("SearchEU\DepartureLocation");
  }


  /**
   * States:
   * NULL (Main) -> SearchU\???
   */
  protected function searchUProcedure() {
    
  }

  /**
   * States:
   * NULL (Main) -> Settings
   */
  protected function settingsProcedure() {
    
  }


}

?>
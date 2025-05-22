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
   * Method to handle the behavior after static input MenuOptions::COMMAND_START
   */
  protected function startProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Questo è il messaggio di start",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


  protected function searchEuProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Invia il nome della località di partenza",
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState("SearchEU\DepartureLocation");
  }


  protected function searchUProcedure() {
    
  }


  protected function settingsProcedure() {
    
  }


}

?>
<?php

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\Keyboards;
use CustomBotName\view\TextMessages;

/**
 * 
 */
class Restart extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::COMMAND_RESTART => "restartProcedure"
  ];


  /**
   * Method to handle the behavior after static input view\MenuOptions::COMMAND_RESTART
   */
  protected function restartProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->destroySearch();

    $this->_Bot->sendMessage([
      'text' => TextMessages::mainMenuFromRestart(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


}

?>
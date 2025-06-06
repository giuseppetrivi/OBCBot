<?php

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\Search;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\Keyboards;
use CustomBotName\view\TextMessages;


class Restart extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::COMMAND_RESTART => "restartProcedure"
  ];


  /**
   * Procedure to handle the restart command (/restart), which has a priority on ony other command
   */
  protected function restartProcedure() {
    $_Search = new Search($this->_User->getUserId());
    $_Search->destroySearch();

    $this->_Bot->sendMessage([
      'text' => TextMessages::mainMenuFromRestart(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


}

?>
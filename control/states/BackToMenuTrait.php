<?php

use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\Keyboards;
use CustomBotName\view\TextMessages;

/**
 * Procedure to come back to the main menu, common to every process
 */
trait BackToMenuTrait {
  protected function backToMenuProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->destroySearch();

    $this->_Bot->sendMessage([
      'text' => TextMessages::mainMenu(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    $this->setNextState(NULL);
  }
}


?>
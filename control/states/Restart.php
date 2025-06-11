<?php

use OBCBot\control\AbstractState;
use OBCBot\entities\api_cotrap\Search;
use OBCBot\view\MenuOptions;
use OBCBot\view\Keyboards;
use OBCBot\view\MainTextMessages;


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
      'text' => MainTextMessages::restarted() . "\n\n" . MainTextMessages::mainMenu(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


}

?>
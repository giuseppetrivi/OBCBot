<?php

use CustomBotName\view;
use CustomBotName\control\AbstractState;
use CustomBotName\view\Keyboards;


/**
 * 
 */
class Restart extends AbstractState {

  protected array $valid_static_inputs = [
    view\MenuOptions::COMMAND_RESTART => "restartProcedure"
  ];


  /**
   * Method to handle the behavior after static input view\MenuOptions::COMMAND_RESTART
   */
  protected function restartProcedure() {
    # Delete all states into database to restart the procedure from the main menu
    $this->_Bot->sendMessage([
      'text' => "This is the forced restart of the bot...",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


}

?>
<?php

use CustomBotName\view;
use CustomBotName\control\AbstractState;
use CustomBotName\view\Keyboards;


/**
 * 
 */
class Main extends AbstractState {

  protected array $valid_static_inputs = [
    view\MenuOptions::COMMAND_START => "startProcedure"
  ];


  /**
   * Method to handle the behavior after static input view\MenuOptions::COMMAND_START
   */
  protected function startProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Hello my friend, this is the start message!",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


}

?>
<?php

namespace SearchEU\DepartureLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\view\MenuOptions;


/**
 * 
 */
class ArrivalLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];

  /**
   * 
   */
  protected function backProcedure() {
    //
  }

}

?>
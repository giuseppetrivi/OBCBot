<?php

namespace SearchEU\DepartureLocation\ArrivalLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocalitaEU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

class DepartureStop extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    // TODO: da modificare...
    /* regex to get words, eventually containing "-", as valid command. this word should be a location */
    $locations_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    $match_result = preg_match($locations_regex, $input_text);
    if ($match_result) {
      $this->function_to_call = "selectDepartureStopProcedure";
      return true;
    }
  }

  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop 
   *  -> SearchEU\DepartureLocation\ArrivalLocation
   */
  protected function backProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $result = $_SearchEU->unsetArrivalLocation();

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseArrivalLocation(),
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState($this->getPreviousState());
  }


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop
   */
  protected function selectDepartureStopProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Procedura di selezione fermata"
    ]);
  }

}

?>
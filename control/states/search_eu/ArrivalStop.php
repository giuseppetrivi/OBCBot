<?php

namespace SearchEU\DepartureLocation\ArrivalLocation\DepartureStop;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationStops;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

class ArrivalStop extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    /* regex to get callback_data like polo_ID */
    $stops_regex = "/^polo_[0-9][0-9]*$/";
    $match_result = preg_match($stops_regex, $input_text);
    if ($match_result) {
      $this->function_to_call = "selectArrivalStopProcedure";
      return true;
    }
  }

  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop 
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop
   */
  protected function backProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->unsetDepartureStop();

    // TODO: da cambiare
    /*$this->_Bot->sendMessage([
      'text' => TextMessages::chooseArrivalLocation(),
      'reply_markup' => Keyboards::getOnlyBack()
    ]);*/

    $this->setNextState($this->getPreviousState());
  }


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop\Datetime
   */
  protected function selectArrivalStopProcedure() {
    $departure_stop_callback_data = $this->_Bot->getInputFromChat()->getText();
    $departure_stop_id = explode("_", $departure_stop_callback_data)[1];

    $_SearchEU = new SearchEU($this->_User->getUserId());

    $_SearchEU->setArrivalStop($departure_stop_id);

    /*
    $_LocationStops = new LocationStops();
    $location_stops_info = $_LocationStops->getValidArrivalLocationStops($departure_stop_id, $arrival_location_id);

    var_dump(json_encode($location_stops_info, JSON_PRETTY_PRINT));

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseArrivalStop(),
      'reply_markup' => InlineKeyboards::locationStops($location_stops_info)
    ]);
    */

    $this->keepThisState();
  }

}

?>
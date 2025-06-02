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

    $search_info = $_SearchEU->getSearchInfo();
    $departure_location_id = $search_info["sea_departure_id"];
    $arrival_location_id = $search_info["sea_arrival_id"];

    $_LocationStops = new LocationStops();
    $location_stops_info = $_LocationStops->getValidDepartureLocationStops($departure_location_id, $arrival_location_id);

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseDepartureStop(),
      'reply_markup' => InlineKeyboards::locationStops($location_stops_info)
    ]);

    $this->setNextState($this->getPreviousState());
  }


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop\Datetime
   */
  protected function selectArrivalStopProcedure() {
    $arrival_stop_callback_data = $this->_Bot->getInputFromChat()->getText();
    $arrival_stop_id = explode("_", $arrival_stop_callback_data)[1];

    $_SearchEU = new SearchEU($this->_User->getUserId());

    $_SearchEU->setArrivalStop($arrival_stop_id);

    // TODO: datetime picker

    $this->_Bot->sendMessage([
      'text' => "Messaggio per fare il pick della data e dell'ora"
    ]);

    $this->setNextState($this->appendNextState("PickDatetime"));
  }

}

?>
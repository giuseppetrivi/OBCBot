<?php

namespace SearchEU\DepartureLocation\ArrivalLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocalitaEU;
use CustomBotName\entities\api_cotrap\LocationStops;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

class DepartureStop extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    /* regex to get callback_data like polo_ID */
    $locations_regex = "/^polo_[0-9][0-9]*$/";
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
    $_SearchEU->unsetArrivalLocation();

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
    $departure_stop_callback_data = $this->_Bot->getInputFromChat()->getText();
    $departure_stop_id = explode("_", $departure_stop_callback_data)[1];

    $_SearchEU = new SearchEU($this->_User->getUserId());

    $_SearchEU->setDepartureStop($departure_stop_id);

    $arrival_location_id = $_SearchEU->getSearchInfo()["sea_arrival_id"];


    $_LocationStops = new LocationStops();
    $location_stops_info = $_LocationStops->getValidArrivalLocationStops($departure_stop_id, $arrival_location_id);

    var_dump(json_encode($location_stops_info, JSON_PRETTY_PRINT));

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseArrivalStop(),
      'reply_markup' => InlineKeyboards::locationStops($location_stops_info)
    ]);

    $this->setNextState($this->appendNextState("ArrivalStop"));
  }

}

?>
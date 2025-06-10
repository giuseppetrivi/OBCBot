<?php

namespace SearchEU\DepartureLocation\ArrivalLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationStopsEU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\entities\telegrambot_sdk_interface\InputTypes;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\Keyboards;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\SearchEUTextMessages;
use BackToMenuTrait;


class DepartureStop extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure",
    MenuOptions::BACK_TO_MENU => "backToMenuProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* regex to get callback_data like stop_ID */
    $stops_regex = "/^stop_[0-9][0-9]*$/";
    if (preg_match($stops_regex, $input_text) && $input_type==InputTypes::CALLBACK_QUERY) {
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
      'text' => SearchEUTextMessages::chooseLocation(false),
      'reply_markup' => Keyboards::getBackAndMenu()
    ]);

    $this->setNextState($this->getPreviousState());
  }

  use BackToMenuTrait;


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop
   */
  protected function selectDepartureStopProcedure() {
    $departure_stop_callback_data = $this->_Bot->getInputFromChat()->getText();
    $departure_stop_id = explode("_", $departure_stop_callback_data)[1];

    // TODO: controllare il departure_stop_id

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->setDepartureStop($departure_stop_id);

    $_LocationStops = new LocationStopsEU();
    $departure_stop_name = $_LocationStops->getStopInfoById($departure_stop_id)["denominazione"];

    $this->_Bot->sendMessage([
      'text' => SearchEUTextMessages::stopSelected($departure_stop_name, true)
    ]);

    $arrival_location_id = $_SearchEU->getSearchInfo()["sea_arrival_id"];

    $_LocationStops = new LocationStopsEU();
    $location_stops_info = $_LocationStops->getValidArrivalLocationStops($departure_stop_id, $arrival_location_id);

    if ($location_stops_info==null || count($location_stops_info)==0) {
      // TODO: valore da controllare e gestire meglio
      $this->_Bot->sendMessage([
        'text' => SearchEUTextMessages::errorInRetriveStops()
      ]);

      $this->backProcedure();
    }
    else {
      $this->_Bot->sendMessage([
        'text' => SearchEUTextMessages::chooseStop(false),
        'reply_markup' => InlineKeyboards::locationStops($location_stops_info)
      ]);

      $this->setNextState($this->appendNextState("ArrivalStop"));
    }

  }

}

?>
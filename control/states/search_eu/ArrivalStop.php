<?php

namespace SearchEU\DepartureLocation\ArrivalLocation\DepartureStop;

use OBCBot\control\AbstractState;
use OBCBot\entities\api_cotrap\LocationStopsEU;
use OBCBot\entities\api_cotrap\SearchEU;
use OBCBot\entities\telegrambot_sdk_interface\InputTypes;
use OBCBot\utilities\DateTimeIT;
use OBCBot\view\MenuOptions;
use OBCBot\view\InlineKeyboards;
use OBCBot\view\SearchEUTextMessages;
use BackToMenuTrait;


class ArrivalStop extends AbstractState {

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

    $_LocationStops = new LocationStopsEU();
    $location_stops_info = $_LocationStops->getValidDepartureLocationStops($departure_location_id, $arrival_location_id);

    $this->_Bot->sendMessage([
      'text' => SearchEUTextMessages::chooseStop(true),
      'reply_markup' => InlineKeyboards::locationStops($location_stops_info)
    ]);

    $this->setNextState($this->getPreviousState());
  }

  use BackToMenuTrait;


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop\Datetime
   */
  protected function selectArrivalStopProcedure() {
    $arrival_stop_callback_data = $this->_Bot->getInputFromChat()->getText();
    $arrival_stop_id = explode("_", $arrival_stop_callback_data)[1];

    // TODO: da verificare arrival_stop_id

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->setArrivalStop($arrival_stop_id);

    $_LocationStops = new LocationStopsEU();
    $arrival_stop_name = $_LocationStops->getStopInfoById($arrival_stop_id)["denominazione"];

    $this->_Bot->sendMessage([
      'text' => SearchEUTextMessages::stopSelected($arrival_stop_name, false)
    ]);

    /* datetime picker keyboard */
    $_SelectedDatetime = new DateTimeIT(date(DateTimeIT::DATABASE_FORMAT));
    $_SearchEU->setDatetime($_SelectedDatetime->databaseFormat());

    $this->_Bot->sendMessage([
      "text" => SearchEUTextMessages::selectDatetime() . "\n\n" . SearchEUTextMessages::summarySelectedDatetime($_SelectedDatetime),
      "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
    ]);

    $this->setNextState($this->appendNextState("PickDatetime"));
  }

}

?>
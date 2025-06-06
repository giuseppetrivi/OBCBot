<?php

namespace SearchU\DepartureLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationsU;
use CustomBotName\entities\api_cotrap\LocationStopsU;
use CustomBotName\entities\api_cotrap\SearchU;
use CustomBotName\entities\telegrambot_sdk_interface\InputTypes;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\Keyboards;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\SearchUTextMessages;
use BackToMenuTrait;

class DepartureStop extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure",
    MenuOptions::BACK_TO_MENU => "backToMenuProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* regex to get words, eventually containing "-", as valid command. this word should be a location */
    $stops_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    if (preg_match($stops_regex, $input_text) && $input_type==InputTypes::MESSAGE) {
      $this->function_to_call = "selectDepartureStopProcedure";
      return true;
    }
  }


  /**
   * States:
   * SearchU\DepartureLocation\DepartureStop -> SearchU\DepartureLocation
   */
  protected function backProcedure() {
    $_SearchU = new SearchU($this->_User->getUserId());
    $_SearchU->unsetDepartureLocation();

    $_LocationsU = new LocationsU();
    $all_urbal_locations = $_LocationsU->getAllUrbanLocations();

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::chooseUrbanLocation(),
      'reply_markup' => InlineKeyboards::urbanLocationsList($all_urbal_locations)
    ]);

    $this->setNextState($this->getPreviousState());
  }

  use BackToMenuTrait;


  /**
   * States:
   * SearchU\DepartureLocation\DepartureStop 
   *  -> SearchU\DepartureLocation\DepartureStop\ArrivalStop
   *     SearchU\DepartureLocation\DepartureStop
   */
  protected function selectDepartureStopProcedure() {
    $stop_to_search = $this->_Bot->getInputFromChat()->getText();

    $_SearchU = new SearchU($this->_User->getUserId());
    $urban_location_id = $_SearchU->getSearchInfo()["sea_departure_id"];

    $_LocationStopsU = new LocationStopsU();
    $all_departure_stops = $_LocationStopsU->getAllValidDepartureStop($urban_location_id);
    $stops_info = $_LocationStopsU->findBestStopNameMatch($all_departure_stops, $stop_to_search);

    $first_stop_code = $stops_info[0]["stop_code"];
    $first_stop_name = $stops_info[0]["stop_name"];
    $first_stop_similarity_perc = $stops_info[0]["similarity_perc"];
    
    
    /* takes the location as valid even if there is not a perfect match */
    if ($first_stop_similarity_perc >= LocationsU::ALMOST_MATCHED) {

      if ($first_stop_similarity_perc >= LocationsU::MATCHED) {
        $this->_Bot->sendMessage([
          'text' => SearchUTextMessages::stopSelected($first_stop_name, true)
        ]);
      }
      else {
        $message_to_send = SearchUTextMessages::maybeYouMeant($first_stop_name) .
          "\n\n" . SearchUTextMessages::stopSelected($first_stop_name, true);
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }
      
      $_SearchU->setDepartureStop($first_stop_code);

      $this->_Bot->sendMessage([
        'text' => SearchUTextMessages::chooseUrbanStop(false),
        'reply_markup' => Keyboards::getBackAndMenu()
      ]);

      $this->setNextState($this->appendNextState("ArrivalStop"));
      
    }
    /* the match between the values ​​in the database and the value sent is not sufficient: the location must be resent */
    else {
      $message_to_send = SearchUTextMessages::alternativeOptions($stops_info) . 
        "\n\n" . SearchUTextMessages::chooseUrbanStop(true, true);
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>
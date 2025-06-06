<?php

namespace SearchEU\DepartureLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationsEU;
use CustomBotName\entities\api_cotrap\LocationStopsEU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\entities\telegrambot_sdk_interface\InputTypes;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\Keyboards;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\SearchEUTextMessages;
use BackToMenuTrait;

class ArrivalLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure",
    MenuOptions::BACK_TO_MENU => "backToMenuProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* regex to get words, eventually containing "-", as valid command. this word should be a location */
    $locations_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    if (preg_match($locations_regex, $input_text) && $input_type==InputTypes::MESSAGE) {
      $this->function_to_call = "selectArrivalLocationProcedure";
      return true;
    }
  }

  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation -> SearchEU\DepartureLocation
   */
  protected function backProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->unsetDepartureLocation();

    $this->_Bot->sendMessage([
      'text' => SearchEUTextMessages::chooseLocation(true),
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState($this->getPreviousState());
  }

  use BackToMenuTrait;


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation 
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop
   *     SearchEU\DepartureLocation\ArrivalLocation
   */
  protected function selectArrivalLocationProcedure() {
    $location_to_search = $this->_Bot->getInputFromChat()->getText();

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $departure_location_id = $_SearchEU->getSearchInfo()["sea_departure_id"];

    $_LocationsEU = new LocationsEU();
    $all_arrival_locations = $_LocationsEU->getArrivalLocationsFromDepartureLocationId($departure_location_id);
    $locations_info = $_LocationsEU->findBestLocationNameMatch($all_arrival_locations, $location_to_search);
    
    $first_location_code = $locations_info[0]["location_code"];
    $first_location_name = $locations_info[0]["location_name"];
    $first_location_similarity_perc = $locations_info[0]["similarity_perc"];
    
    
    /* takes the location as valid even if there is not a perfect match */
    if ($first_location_similarity_perc >= LocationsEU::ALMOST_MATCHED) {

      if ($first_location_similarity_perc >= LocationsEU::MATCHED) {
        $this->_Bot->sendMessage([
          'text' => SearchEUTextMessages::locationSelected($first_location_name, false)
        ]);
      }
      else { 
        $message_to_send = SearchEUTextMessages::maybeYouMeant($first_location_name) . 
          "\n\n" . SearchEUTextMessages::alternativeOptions(array_slice($locations_info, 1)) .
          "\n" . SearchEUTextMessages::locationSelected($first_location_name, false);          
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }

      $_SearchEU->setArrivalLocation($first_location_code);
      
      $_LocationStops = new LocationStopsEU();
      $location_stops_info = $_LocationStops->getValidDepartureLocationStops($departure_location_id, $first_location_code);

      if ($location_stops_info==null || count($location_stops_info)==0) {
        // TODO: valore da controllare e gestire meglio
        $this->_Bot->sendMessage([
          'text' => SearchEUTextMessages::errorInRetriveStops()
        ]);

        $this->backProcedure();
      }
      else {
        $this->_Bot->sendMessage([
          'text' => SearchEUTextMessages::chooseStop(true),
          'reply_markup' => InlineKeyboards::locationStops($location_stops_info)
        ]);

        $this->setNextState($this->appendNextState("DepartureStop"));
      }
      
    }
    /* the match between the values ​​in the database and the value sent is not sufficient: the location must be resent */
    else {
      $message_to_send = SearchEUTextMessages::maybeYouMeant($location_to_search) . 
        "\n\n" . SearchEUTextMessages::chooseLocation(false, true);
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>
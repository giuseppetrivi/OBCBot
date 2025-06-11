<?php

namespace SearchEU;

use OBCBot\control\AbstractState;
use OBCBot\entities\api_cotrap\LocationsEU;
use OBCBot\entities\api_cotrap\SearchEU;
use OBCBot\entities\telegrambot_sdk_interface\InputTypes;
use OBCBot\view\MenuOptions;
use OBCBot\view\Keyboards;
use OBCBot\view\MainTextMessages;
use OBCBot\view\SearchEUTextMessages;


class DepartureLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* regex to get words, eventually containing "-", as valid command. this word should be a location */
    $locations_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    if (preg_match($locations_regex, $input_text) && $input_type==InputTypes::MESSAGE) {
      $this->function_to_call = "selectDepartureLocationProcedure";
      return true;
    }
  }


  /**
   * States:
   * SearchEU\DepartureLocation -> NULL (Main)
   */
  protected function backProcedure() {
    $_SearchEU = new SearchEU($this->_User->getUserId());
    $_SearchEU->destroySearch();

    $this->_Bot->sendMessage([
      'text' => MainTextMessages::mainMenu(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    $this->setNextState(NULL);
  }


  /**
   * States:
   * SearchEU\DepartureLocation 
   *  -> SearchEU\DepartureLocation\ArrivalLocation
   *     SearchEU\DepartureLocation
   */
  protected function selectDepartureLocationProcedure() {
    $location_to_search = $this->_Bot->getInputFromChat()->getText();

    $_LocationsEU = new LocationsEU();
    $all_departure_locations = $_LocationsEU->getAllDepartureLocations();
    $locations_info = $_LocationsEU->findBestLocationNameMatch($all_departure_locations, $location_to_search);

    $first_location_code = $locations_info[0]["location_code"];
    $first_location_name = $locations_info[0]["location_name"];
    $first_location_similarity_perc = $locations_info[0]["similarity_perc"];
    
    
    /* takes the location as valid even if there is not a perfect match */
    if ($first_location_similarity_perc >= LocationsEU::ALMOST_MATCHED) {

      if ($first_location_similarity_perc >= LocationsEU::MATCHED) {
        $this->_Bot->sendMessage([
          'text' => SearchEUTextMessages::locationSelected($first_location_name, true)
        ]);
      }
      else {
        $message_to_send = SearchEUTextMessages::maybeYouMeant($first_location_name) . 
          "\n\n" . SearchEUTextMessages::alternativeOptions(array_slice($locations_info, 1)) .
          "\n" . SearchEUTextMessages::locationSelected($first_location_name, true);
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }

      $_SearchEU = new SearchEU($this->_User->getUserId());
      $_SearchEU->setDepartureLocation($first_location_code);

      $this->_Bot->sendMessage([
        'text' => SearchEUTextMessages::chooseLocation(false),
        'reply_markup' => Keyboards::getBackAndMenu()
      ]);

      $this->setNextState($this->appendNextState("ArrivalLocation"));
      
    }
    /* the match between the values ​​in the database and the value sent is not sufficient: the location must be resent */
    else {
      $message_to_send = SearchEUTextMessages::entityNotFound($location_to_search) . 
        "\n\n" . SearchEUTextMessages::chooseLocation(true, true);
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>
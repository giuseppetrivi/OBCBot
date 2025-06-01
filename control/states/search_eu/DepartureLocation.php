<?php

namespace SearchEU;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationsEU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;


class DepartureLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    /* regex to get words, eventually containing "-", as valid command. this word should be a location */
    $locations_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    $match_result = preg_match($locations_regex, $input_text);
    if ($match_result) {
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
    $result = $_SearchEU->destroySearch();

    $this->_Bot->sendMessage([
      'text' => TextMessages::mainMenu(),
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
          'text' => TextMessages::departureLocationMatched($first_location_name)
        ]);
      }
      else {
        $message_to_send = TextMessages::locationAlmostMatched($first_location_name) . 
          "\n\n" . TextMessages::alternativeLocations(array_slice($locations_info, 1)) .
          "\n" . TextMessages::departureLocationMatched($first_location_name);
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }

      $_SearchEU = new SearchEU($this->_User->getUserId());
      $_SearchEU->setDepartureLocation($first_location_code);

      $this->_Bot->sendMessage([
        'text' => TextMessages::chooseArrivalLocation(),
        'reply_markup' => Keyboards::getOnlyBack()
      ]);

      $this->setNextState($this->appendNextState("ArrivalLocation"));
      
    }
    /* the match between the values ​​in the database and the value sent is not sufficient: the location must be resent */
    else {
      $message_to_send = TextMessages::locationNotMatched($location_to_search) . 
        "\n\n" . TextMessages::chooseDepartureLocationAgain();
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>
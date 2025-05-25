<?php

namespace SearchEU;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocalitaEU;
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
    //regex per avere solo lettere e il carattere "-"
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
    //var_dump($result);

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

    $_LocalitaEU = new LocalitaEU();
    $all_departure_locations = $_LocalitaEU->getAllDepartureLocations();
    $location_info = $_LocalitaEU->findBestLocationNameMatch($all_departure_locations, $location_to_search);

    $first_location_code = $location_info["location_code"];
    $first_location_name = $location_info["location_name"];
    $first_location_similarity_perc = $location_info["similarity_perc"];
    
    
    /* pur non essendoci corrispondenza perfetta, la località inviata è valida */

    if ($first_location_similarity_perc >= LocalitaEU::ALMOST_MATCHED) {

      if ($first_location_similarity_perc >= LocalitaEU::MATCHED) {
        $this->_Bot->sendMessage([
          'text' => TextMessages::departureLocationValid100($first_location_name)
        ]);
      }
      else { 
        $message_to_send = TextMessages::departureLocationValidNot100($first_location_name) . "\n\n" . TextMessages::departureLocationValid100($first_location_name);
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }

      $_SearchEU = new SearchEU($this->_User->getUserId());
      $result = $_SearchEU->setDepartureLocation($first_location_code);

      $this->_Bot->sendMessage([
        'text' => TextMessages::chooseArrivalLocation(),
        'reply_markup' => Keyboards::getOnlyBack()
      ]);
      
      $this->setNextState($this->appendNextState("ArrivalLocation"));
      
    }
    /* c'è poca corrispondenza tra il db e la località inviata, bisogna riinviarla */
    else {
      $message_to_send = TextMessages::departureLocationNotValid($location_to_search) . "\n\n" . TextMessages::chooseDepartureLocationAgain();
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>
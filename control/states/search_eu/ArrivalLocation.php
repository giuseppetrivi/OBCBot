<?php

namespace SearchEU\DepartureLocation;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocalitaEU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

class ArrivalLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    //regex per avere solo lettere e il carattere -
    $locations_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    $match_result = preg_match($locations_regex, $input_text);
    if ($match_result) {
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
    $result = $_SearchEU->unsetDepartureLocation();

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseDepartureLocation(),
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState($this->getPreviousState());
  }


  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation 
   *  -> SearchEU\DepartureLocation\ArrivalLocation\???
   */
  protected function selectArrivalLocationProcedure() {
    $location_to_search = $this->_Bot->getInputFromChat()->getText();

    $_LocalitaEU = new LocalitaEU();
    $all_arrival_locations = $_LocalitaEU->getAllArrivalLocations($this->_User->getUserId());
    $locations_info = $_LocalitaEU->findBestLocationNameMatch($all_arrival_locations, $location_to_search);
    
    $first_location_code = $locations_info[0]["location_code"];
    $first_location_name = $locations_info[0]["location_name"];
    $first_location_similarity_perc = $locations_info[0]["similarity_perc"];
    
    
    /* pur non essendoci corrispondenza perfetta, la località inviata è valida */
    if ($first_location_similarity_perc >= LocalitaEU::ALMOST_MATCHED) {
      if ($first_location_similarity_perc >= LocalitaEU::MATCHED) {
        $this->_Bot->sendMessage([
          'text' => TextMessages::arrivalLocationMatched($first_location_name)
        ]);
      }
      else { 
        $message_to_send = TextMessages::arrivalLocationAlmostMatched($first_location_name) . 
          "\noppure cercavi:\n" .
          TextMessages::alternativeLocations(array_slice($locations_info, 1)) .
          "\n" .
          TextMessages::arrivalLocationMatched($first_location_name);
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }

      $_SearchEU = new SearchEU($this->_User->getUserId());
      $result = $_SearchEU->setArrivalLocation($first_location_code);

      $this->_Bot->sendMessage([
        'text' => "Prossima richiesta ...",
        'reply_markup' => Keyboards::getOnlyBack()
      ]);

      $this->keepThisState(); // !!
      
    }
    /* c'è poca corrispondenza tra il db e la località inviata, bisogna riinviarla */
    else {
      $message_to_send = TextMessages::arrivalLocationNotMatched($location_to_search) . 
        "\n\n" . 
        TextMessages::chooseArrivalLocationAgain();
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>
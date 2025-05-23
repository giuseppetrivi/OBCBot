<?php

namespace SearchEU;

use CustomBotName\control\AbstractState;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

/**
 * 
 */
class DepartureLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    //regex per avere solo lettere e il carattere -
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
    $this->_Bot->sendMessage([
      'text' => TextMessages::mainMenu(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    $this->setNextState(NULL);
  }


  /**
   * DA MODIFICARE
   * States:
   * SearchEU\DepartureLocation 
   *  -> SearchEU\DepartureLocation\ArrivalLocation
   *     SearchEU\DepartureLocation
   */
  protected function selectDepartureLocationProcedure() {
    $location_to_search = $this->_Bot->getInputFromChat()->getText();

    // TODO: Trasferire questo codice in una funzione (classe) apposita
    $db_localita_eu_json = file_get_contents("local_db/localita_eu.json");
    $db_localita_eu_array = json_decode($db_localita_eu_json, true);
    $count_localita_eu = count($db_localita_eu_array);

    $assoc_array_perc = [];
    for ($i=0; $i<$count_localita_eu; $i++) {
      $similarity_perc = 0;
      $location_name = $db_localita_eu_array[$i]["denominazione"];

      /* toglie gli spazi bianchi da inizio e fine stringa e mette tutto in minuscolo */
      $formatted_location_name = trim(strtolower($location_name));
      $formatted_location_to_search = trim(strtolower($location_to_search));
      similar_text($formatted_location_name, $formatted_location_to_search, $similarity_perc);

      $assoc_array_perc[$location_name] = $similarity_perc;
    }

    arsort($assoc_array_perc);
    $first_location_name = array_key_first($assoc_array_perc);
    $first_location_similarity_perc = $assoc_array_perc[$first_location_name];
    
    
    /* pur non essendoci corrispondenza perfetta, la località inviata è valida */
    if ($first_location_similarity_perc >= 60 ) {
      if ($first_location_similarity_perc > 95) {
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
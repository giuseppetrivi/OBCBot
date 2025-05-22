<?php

namespace SearchEU;

use CustomBotName\control\AbstractState;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;


/**
 * 
 */
class DepartureLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    //regex per avere solo lettere e al massimo il carattere -
    $locations_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    $match_result = preg_match($locations_regex, $input_text);
    if ($match_result) {
      $this->function_to_call = "selectLocationProcedure";
      return true;
    }
  }


  /**
   * 
   */
  protected function backProcedure() {
    $this->_Bot->sendMessage([
      'text' => "📜 Menu principale",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


  /**
   * DA MODIFICARE
   */
  protected function selectLocationProcedure() {
    $location_to_search = $this->_Bot->getInputFromChat()->getText();

    $db_localita_eu_json = file_get_contents("local_db/localita_eu.json");
    $db_localita_eu_array = json_decode($db_localita_eu_json, true);
    $count_localita_eu = count($db_localita_eu_array);

    $assoc_array_perc = [];
    for ($i=0; $i<$count_localita_eu; $i++) {
      $similarity_perc = 0;
      $location_name = $db_localita_eu_array[$i]["denominazione"];

      /* Toglie gli spazi bianchi da inizio e fine stringa e mette tutto in minuscolo */
      $s1 = trim(strtolower($location_name));
      $s2 = trim(strtolower($location_to_search));
      similar_text($s1, $s2, $similarity_perc);

      $assoc_array_perc[$location_name] = $similarity_perc;
    }

    arsort($assoc_array_perc);

    $key = array_key_first($assoc_array_perc);
    $this->_Bot->sendMessage([
      'text' => $key . " => " . $assoc_array_perc[$key]
    ]);


  }

}

?>
<?php

namespace SearchEU\DepartureLocation;

use CustomBotName\control\AbstractState;
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
    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseDepartureLocation(),
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    var_dump($this->getPreviousState());
    $this->setNextState($this->getPreviousState());
  }


  /**
   * DA MODIFICARE COMPLETAMENTE
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation 
   *  -> SearchEU\DepartureLocation\ArrivalLocation\???
   */
  protected function selectArrivalLocationProcedure() {
    $location_to_search = $this->_Bot->getInputFromChat()->getText();

    $db_localita_eu_json = file_get_contents("local_db/localita_eu.json");
    $db_localita_eu_array = json_decode($db_localita_eu_json, true);
    $count_localita_eu = count($db_localita_eu_array);

    $assoc_array_perc = [];
    for ($i=0; $i<$count_localita_eu; $i++) {
      $similarity_perc = 0;
      $location_name = $db_localita_eu_array[$i]["denominazione"];

      /* Toglie gli spazi bianchi da inizio e fine stringa e mette tutto in minuscolo */
      $formatted_location_name = trim(strtolower($location_name));
      $formatted_location_to_search = trim(strtolower($location_to_search));
      similar_text($formatted_location_name, $formatted_location_to_search, $similarity_perc);

      $assoc_array_perc[$location_name] = $similarity_perc;
    }

    arsort($assoc_array_perc);
    $first_location_name = array_key_first($assoc_array_perc);
    $first_location_similarity_perc = $assoc_array_perc[$first_location_name];

    $message_to_send = "Hai selezionato <b>$first_location_name</b> come città di partenza";
    if ($first_location_similarity_perc < 95) {
      $message_to_send = "Forse intendevi <i>$location_to_search</i> → <b>$first_location_name</b>\n\n" . $message_to_send;
    }


    $this->_Bot->sendMessage([
      'text' => $message_to_send
    ]);

    $this->_Bot->sendMessage([
      'text' => "➤ Invia il nome della località di <u>arrivo</u>",
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState($this->appendNextState("ArrivalLocation"));
  }

}

?>
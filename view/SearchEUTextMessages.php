<?php

namespace OBCBot\view;

/**
 * 
 */
class SearchEUTextMessages extends SearchTextMessages {

  /**
   * Message before sending (again, if $again=true) a departure (or arrival, if $departure=false) location 
   */
  public static function chooseLocation(bool $departure, bool $again=false) {
    $type_of_location_text = $departure ? "località di partenza" : "località di arrivo";
    $again_text = $again ? " nuovamente" : "";
    return "📍 Invia" . $again_text . " il nome della <b>" . $type_of_location_text . "</b>";
  }
  /**
   * Result message after selecting a location name for the departure (or arrival, if $departure=false) 
   */
  public static function locationSelected(string $location_name, bool $departure) {
    $type_of_location_text = $departure ? "località di partenza" : "località di arrivo";
    return "✅ Hai selezionato <b>$location_name</b> come " . $type_of_location_text;
  }


  public static function alternativeOptions(array $options_info) {
    $text = "🤔 Oppure cercavi:\n";
    foreach ($options_info as $info) {
      $text .= "• <code>" . $info["location_name"] . "</code>\n";
    }
    return $text;
  }

  public static function entityNotFound(string $entity_to_search) {
    return "❌  La località <i>$entity_to_search</i> non è tra le scelte possibili...";
  }


  /**
   * Message before selecting a departure (or arrival, if $departure=false) stop 
   */
  public static function chooseStop(bool $departure) {
    $type_of_stop_text = $departure ? "fermata di partenza" : "fermata di arrivo";
    return "🛑 Seleziona la <b>" . $type_of_stop_text . "</b> tra quelle proposte";
  }

  public static function errorInRetriveStops() {
    return "⚠️ Si è verificato un errore nella ricerca delle fermate";
  }

}
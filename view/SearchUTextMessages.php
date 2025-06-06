<?php

namespace CustomBotName\view;

use CustomBotName\entities\DateTimeIT;

/**
 * 
 */
class SearchUTextMessages extends SearchTextMessages {

  public static function urbanSearchHeader() {
    return "🌇 Questo è il procedimento per cercare le tratte urbane";
  }
  public static function chooseUrbanLocation() {
    return "📍 Scegli una <b>località tra le seguenti</b>";
  }
  public static function locationSelected(string $location_name) {
    return "✅ Hai selezionato <b>$location_name</b> come località";
  }


  /**
   * Message before sending (again, if $again=true) a departure (or arrival, if $departure=false) stop 
   */
  public static function chooseUrbanStop(bool $departure, bool $again=false) {
    $type_of_location_text = $departure ? "fermata di partenza" : "fermata di arrivo";
    $again_text = $again ? " nuovamente" : "";
    return "🛑 Invia" . $again_text . " il nome della <b>" . $type_of_location_text . "</b>";
  }

  public static function alternativeOptions(array $options_info) {
    $text = "🤔 Forse cercavi:\n";
    foreach ($options_info as $info) {
      $text .= "• <code>" . $info["stop_name"] . "</code>\n";
    }
    return $text;
  }

  public static function entityNotFound(string $stop_to_search) {
    return "❌  La fermata <i>$stop_to_search</i> non è tra le scelte possibili...";
  }

}
<?php

namespace CustomBotName\view;

use CustomBotName\entities\DateTimeIT;

/**
 * 
 */
abstract class SearchTextMessages {
  
  /* This class is not callable, so constructor is private */
  protected function __construct() {}


  /**
   * @param string $name Can bea a location or a stop
   */
  public static function maybeYouMeant(string $name) {
    return "❓ Forse intendevi <i>$name</i>";
  }

  /**
   * Format a list of alternative options (between locations or stops)
   */
  abstract public static function alternativeOptions(array $options_info);

  abstract public static function entityNotFound(string $entity_to_search);


  public static function selectDatetime() {
    return "⌛ Seleziona la <b>data</b> e l'<b>ora</b> di partenza";
  }

  public static function summarySelectedDatetime(DateTimeIT $_SelectedDatetime) {
    return "📅  Data selezionata: <b>" . $_SelectedDatetime->getLiteralWeekDay() . " " . $_SelectedDatetime->format("d") . " " . $_SelectedDatetime->getLiteralMonth() . " " . $_SelectedDatetime->format("Y") . "</b>\n" .
      "🕒  Ora selezionata:   <b>" . $_SelectedDatetime->format("H:i") . "</b>";
  }

  /**
   * 
   */
  public static function showSearchResults($search_results, $_Datetime) {
    $first = $search_results[0];
    $departure_position = "https://www.google.com/maps?q=" . $first["latitudinePoloPartenza"] . "," . $first["longitudinePoloPartenza"];
    $arrival_position = "https://www.google.com/maps?q=" . $first["latitudinePoloArrivo"] . "," . $first["longitudinePoloArrivo"];

    $header = "🌐 Risultati per il giorno <b>" . $_Datetime->getLiteralWeekDay() . " " . $_Datetime->format("d") . " " . $_Datetime->getLiteralMonth() . " " . $_Datetime->format("Y") . "</b>"
      . ", dalle ore <b>" . $_Datetime->format("H:00") . "</b> in poi...\n\n"
      . "╒ <b>" . $first["localitaPartenza"] . "</b>, <a href='$departure_position'>" . $first["denominazionePartenza"] . "</a>\n"
      . "╘ <b>" . $first["localitaArrivo"] . "</b>, <a href='$arrival_position'>" . $first["denominazioneArrivo"] . "</a>\n\n"
      . "🚌  <i>" . $first["azienda1"] . "</i>\n\n";

    $timetables = "";
    foreach($search_results as $ride) {
      if ($ride["oraPartenza"] < $_Datetime->format("H:00")) {
        continue;
      }

      $type = "";
      if ($ride["tipologiaFrequenzaCorsa1"]==2) {
        $type = "[<i>Scolastica</i>]";
      }
      $timetables .= "🕒  <b>" . $ride["oraPartenza"] . " ➝ " . $ride["oraArrivo"] . "</b> (" . $ride["durata"] . ") $type\n"
        . "💰  <i>€ " . number_format($ride["prezzoUnitario"], 2) . "</i>\n\n";
    }

    return $header . $timetables;
  }

  public static function noSearchResults() {
    return "⚠️ Nessun risultato disponibile. Prova a cambiare qualche parametro di ricerca";
  }

  /**
   * Result message after selecting a stop name for the departure (or arrival, if $departure=false) 
   */
  public static function stopSelected(string $stop_name, bool $departure) {
    $type_of_location_text = $departure ? "fermata di partenza" : "fermata di arrivo";
    return "✅ Hai selezionato <b>$stop_name</b> come " . $type_of_location_text;
  }
}